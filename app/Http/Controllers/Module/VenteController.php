<?php

namespace App\Http\Controllers\Module;

use App\Http\Controllers\Controller;

use App\Models\LigneVente;
use App\Models\Vente;
use App\Models\Produit;
use App\Models\Client;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class VenteController extends Controller
{
    public function index()
    {
        $ventes = Vente::with(['lignes.produit', 'user', 'client'])
            ->where('magasin_id', session('magasin_actif_id'))
            ->latest()
            ->paginate(20);

        return view('module.ventes.index', compact('ventes'));
    }

    public function create()
    {
        $produits = Produit::where('magasin_id', session('magasin_actif_id'))->get();
        $clients = Client::where('magasin_id', session('magasin_actif_id'))->get();

        return view('module.ventes.create', compact('produits', 'clients'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'produits' => 'required|array|min:1',
            'quantites' => 'required|array',
            
            'client_id' => 'nullable|exists:clients,id',
            'remise' => 'nullable|numeric|min:0',
            'mode_paiement' => 'required|in:cash,credit',
        ]);

        $magasinId = session('magasin_actif_id');
        $remise = $request->remise ?? 0;
        $total = 0;

        // Vérification de stock
        foreach ($request->produits as $i => $produitId) {
            $quantite = $request->quantites[$i];
            $stock = Stock::where('produit_id', $produitId)->where('magasin_id', $magasinId)->first();

            if (!$stock || $stock->quantite < $quantite) {
                return back()->with('error', 'Stock insuffisant pour le produit sélectionné.');
            }
        }

        DB::transaction(function () use ($request, $magasinId, $remise, &$total) {
            $vente = Vente::create([
                'user_id' => Auth::id(),
                'magasin_id' => $magasinId,
                'client_id' => $request->client_id,
                'remise' => $remise,
                'total' => 0, // temporaire
                'mode_paiement' => $request->mode_paiement,
            ]);

            foreach ($request->produits as $i => $produitId) {
                $quantite = $request->quantites[$i];
                $prix = Produit::find($produitId)->prix_vente;
                $subtotal = $quantite * $prix;
                $total += $subtotal;

                LigneVente::create([
                    'vente_id' => $vente->id,
                    'produit_id' => $produitId,
                    'quantite' => $quantite,
                    'prix_unitaire' => $prix,
                ]);

                // mise à jour stock
                $stock = Stock::where('produit_id', $produitId)->where('magasin_id', $magasinId)->first();
                $stock->decrement('quantite', $quantite);
            }

            $vente->update(['total' => $total - $remise]);

            if ($request->mode_paiement === 'credit') {
                Credit::create([
                    'vente_id' => $vente->id,
                    'client_id' => $request->client_id,
                    'magasin_id' => $magasinId,
                    'montant' => $total - $remise,
                    'echeance' => now()->addDays(15),
                ]);
            }
        });

        return redirect()->route('module.ventes.index')->with('success', 'Vente enregistrée.');
    }


    public function edit(Vente $vente)
    {
        if ($vente->magasin_id != session('magasin_actif_id')) {
            abort(403);
        }

        $produits = Produit::where('magasin_id', session('magasin_actif_id'))->get();
        $clients = Client::where('magasin_id', session('magasin_actif_id'))->get();

        return view('module.ventes.edit', compact('vente', 'produits', 'clients'));
    }

    public function update(Request $request, Vente $vente)
    {
        if ($vente->magasin_id != session('magasin_actif_id')) abort(403);

        $request->validate([
            'produits' => 'required|array|min:1',
            'quantites' => 'required|array',
            
            'client_id' => 'nullable|exists:clients,id',
            'remise' => 'nullable|numeric|min:0',
            'mode_paiement' => 'required|in:cash,credit',
        ]);

        $magasinId = $vente->magasin_id;
        $remise = $request->remise ?? 0;
        $total = 0;

        DB::transaction(function () use ($request, $vente, $magasinId, $remise, &$total) {
            // rétablir stock
            foreach ($vente->lignes as $ligne) {
                $stock = Stock::where('produit_id', $ligne->produit_id)->where('magasin_id', $magasinId)->first();
                if ($stock) $stock->increment('quantite', $ligne->quantite);
            }

            $vente->lignes()->delete();

            foreach ($request->produits as $i => $produitId) {
                $quantite = $request->quantites[$i];
                $prix = Produit::find($produitId)->prix_vente;
                $subtotal = $quantite * $prix;
                $total += $subtotal;

                LigneVente::create([
                    'vente_id' => $vente->id,
                    'produit_id' => $produitId,
                    'quantite' => $quantite,
                    'prix_unitaire' => $prix,
                ]);

                $stock = Stock::where('produit_id', $produitId)->where('magasin_id', $magasinId)->first();
                $stock->decrement('quantite', $quantite);
            }

            $vente->update([
                'client_id' => $request->client_id,
                'remise' => $remise,
                'total' => $total - $remise,
                'mode_paiement' => $request->mode_paiement,
            ]);
        });

        return redirect()->route('module.ventes.index')->with('success', 'Vente modifiée.');
    }


    public function destroy(Vente $vente)
    {
        if ($vente->magasin_id != session('magasin_actif_id')) abort(403);

        foreach ($vente->lignes as $ligne) {
            $stock = Stock::where('produit_id', $ligne->produit_id)->where('magasin_id', $vente->magasin_id)->first();
            if ($stock) $stock->increment('quantite', $ligne->quantite);
        }

        $vente->lignes()->delete();
        $vente->delete();

        return back()->with('success', 'Vente supprimée et stock rétabli.');
    }


}
