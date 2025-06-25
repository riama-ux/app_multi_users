<?php

namespace App\Http\Controllers\Module;

use App\Http\Controllers\Controller;

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
        $ventes = Vente::with(['produit', 'user', 'client'])
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
            'produit_id' => 'required|exists:produits,id',
            'client_id' => 'nullable|exists:clients,id',
            'quantite' => 'required|integer|min:1',
            'remise' => 'nullable|numeric|min:0',
            'mode_paiement' => 'required|in:cash,credit',
        ]);

        $magasinId = session('magasin_actif_id');
        $produitId = $request->produit_id;
        $quantiteDemandee = $request->quantite;

        // Vérification du stock
        $stock = Stock::where('produit_id', $produitId)
                      ->where('magasin_id', $magasinId)
                      ->first();

        if (!$stock || $stock->quantite < $quantiteDemandee) {
            return back()->with('error', 'Stock insuffisant pour ce produit.');
        }

        // Calculs
        $prixUnitaire = Produit::find($produitId)->prix_vente;
        $remise = $request->remise ?? 0;
        $total = ($prixUnitaire * $quantiteDemandee) - $remise;

        DB::transaction(function () use ($request, $magasinId, $prixUnitaire, $total, $stock) {
            // Création de la vente
            Vente::create([
                'produit_id' => $request->produit_id,
                'user_id' => Auth::id(),
                'magasin_id' => $magasinId,
                'client_id' => $request->client_id,
                'quantite' => $request->quantite,
                'prix_unitaire' => $prixUnitaire,
                'remise' => $request->remise ?? 0,
                'total' => $total,
                'mode_paiement' => $request->mode_paiement,
            ]);

            // Décrémentation du stock
            $stock->decrement('quantite', $request->quantite);

            // Création du crédit si paiement en crédit
            if ($request->mode_paiement === 'credit') {
                Credit::create([
                    'vente_id' => $vente->id,
                    'client_id' => $request->client_id,
                    'magasin_id' => $magasinId,
                    'montant' => $total,
                    'echeance' => now()->addDays(15), // À adapter selon ta logique métier
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
        if ($vente->magasin_id != session('magasin_actif_id')) {
            abort(403);
        }

        $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'client_id' => 'nullable|exists:clients,id',
            'quantite' => 'required|integer|min:1',
            'remise' => 'nullable|numeric|min:0',
            'mode_paiement' => 'required|in:cash,credit',
        ]);

        $stock = Stock::where('produit_id', $vente->produit_id)
                    ->where('magasin_id', $vente->magasin_id)
                    ->first();

        if (!$stock) {
            return back()->with('error', 'Stock introuvable.');
        }

        // ⚠️ Ajustement du stock
        $ancienneQuantite = $vente->quantite;
        $nouvelleQuantite = $request->quantite;

        $ecart = $nouvelleQuantite - $ancienneQuantite;

        if ($ecart > 0 && $stock->quantite < $ecart) {
            return back()->with('error', 'Stock insuffisant pour augmenter la quantité.');
        }

        // Met à jour le stock
        $stock->quantite -= $ecart;
        $stock->save();

        $prixUnitaire = Produit::find($request->produit_id)->prix_vente;
        $remise = $request->remise ?? 0;
        $total = ($prixUnitaire * $nouvelleQuantite) - $remise;

        $vente->update([
            'produit_id' => $request->produit_id,
            'client_id' => $request->client_id,
            'quantite' => $nouvelleQuantite,
            'remise' => $remise,
            'prix_unitaire' => $prixUnitaire,
            'total' => $total,
            'mode_paiement' => $request->mode_paiement,
        ]);

        return redirect()->route('module.ventes.index')->with('success', 'Vente modifiée.');
    }

    public function destroy(Vente $vente)
    {
        if ($vente->magasin_id != session('magasin_actif_id')) {
            abort(403);
        }

        $stock = Stock::where('produit_id', $vente->produit_id)
                    ->where('magasin_id', $vente->magasin_id)
                    ->first();

        if ($stock) {
            $stock->increment('quantite', $vente->quantite);
        }

        $vente->delete();

        return back()->with('success', 'Vente supprimée et stock rétabli.');
    }

}
