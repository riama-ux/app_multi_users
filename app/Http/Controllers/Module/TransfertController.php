<?php

namespace App\Http\Controllers\Module;

use App\Http\Controllers\Controller;

use App\Models\Transfert;
use App\Models\LigneTransfert;
use App\Models\Magasin;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransfertController extends Controller
{
    public function index()
    {
        $magasinId = session('magasin_actif_id');

        $transferts = Transfert::with(['magasinSource', 'magasinDestination', 'user'])
            ->where(function ($query) use ($magasinId) {
                // Affiche les transferts où le magasin actif est source ou destination
                $query->where('magasin_source_id', $magasinId)
                      ->orWhere('magasin_destination_id', $magasinId);
            })
            ->orderByDesc('date_transfert')
            ->paginate(20);

        return view('module.transferts.index', compact('transferts'));
    }

    public function create()
    {
        $magasinId = session('magasin_actif_id');

        $magasins = Magasin::where('id', '!=', $magasinId)->get(); // pour choisir la destination (différente de source)
        $produits = Produit::whereHas('stocks', function ($q) use ($magasinId) {
            $q->where('magasin_id', $magasinId)->where('quantite', '>', 0);
        })->get();

        return view('module.transferts.create', compact('magasins', 'produits'));
    }

    public function store(Request $request)
    {
        $magasinId = session('magasin_actif_id');

        $request->validate([
            'magasin_destination_id' => 'required|exists:magasins,id|different:magasin_source_id',
            'date_transfert' => 'required|date',
            'produits' => 'required|array|min:1',
            'quantites' => 'required|array|min:1',
        ]);

        // Vérifier que la quantité demandée est dispo dans stock source
        foreach ($request->produits as $index => $produitId) {
            $quantiteDemandee = $request->quantites[$index];
            $stock = \App\Models\Stock::where('produit_id', $produitId)
                ->where('magasin_id', $magasinId)
                ->first();

            if (!$stock || $stock->quantite < $quantiteDemandee) {
                return back()->with('error', "Stock insuffisant pour le produit ID $produitId")->withInput();
            }
        }

        DB::transaction(function () use ($request, $magasinId) {
            $transfert = Transfert::create([
                'magasin_source_id' => $magasinId,
                'magasin_destination_id' => $request->magasin_destination_id,
                'user_id' => auth()->id(),
                'date_transfert' => $request->date_transfert,
                'statut' => 'en attente',
            ]);

            foreach ($request->produits as $index => $produitId) {
                LigneTransfert::create([
                    'transfert_id' => $transfert->id,
                    'produit_id' => $produitId,
                    'quantite' => $request->quantites[$index],
                ]);
            }
        });

        return redirect()->route('module.transferts.index')->with('success', 'Transfert créé avec succès.');
    }

    public function show(Transfert $transfert)
    {
        $magasinId = session('magasin_actif_id');

        if (auth()->user()->role !== 'Admin' && $transfert->magasin_source_id !== $magasinId && $transfert->magasin_destination_id !== $magasinId) {
            abort(403);
        }


        $transfert->load('lignes.produit', 'magasinSource', 'magasinDestination', 'user');

        return view('module.transferts.show', compact('transfert'));
    }

    public function edit(Transfert $transfert)
    {
        $magasinId = session('magasin_actif_id');

        if ($transfert->magasin_source_id !== $magasinId) {
            abort(403); // Seul magasin source peut éditer
        }

        if ($transfert->statut !== 'en attente') {
            return redirect()->route('module.transferts.index')->with('error', 'Impossible de modifier un transfert déjà validé ou refusé.');
        }

        $magasins = Magasin::where('id', '!=', $magasinId)->get();
        $produits = Produit::whereHas('stocks', function ($q) use ($magasinId) {
            $q->where('magasin_id', $magasinId);
        })->get();

        $transfert->load('lignes');

        return view('module.transferts.edit', compact('transfert', 'magasins', 'produits'));
    }

    public function update(Request $request, Transfert $transfert)
    {
        $magasinId = session('magasin_actif_id');

        if ($transfert->magasin_source_id !== $magasinId) {
            abort(403);
        }

        if ($transfert->statut !== 'en attente') {
            return redirect()->route('module.transferts.index')->with('error', 'Impossible de modifier un transfert déjà validé ou refusé.');
        }

        $request->validate([
            'magasin_destination_id' => 'required|exists:magasins,id|different:magasin_source_id',
            'date_transfert' => 'required|date',
            'produits' => 'required|array|min:1',
            'quantites' => 'required|array|min:1',
        ]);

        // Vérification stock
        foreach ($request->produits as $index => $produitId) {
            $quantiteDemandee = $request->quantites[$index];
            $stock = \App\Models\Stock::where('produit_id', $produitId)
                ->where('magasin_id', $magasinId)
                ->first();

            if (!$stock || $stock->quantite < $quantiteDemandee) {
                return back()->with('error', "Stock insuffisant pour le produit ID $produitId")->withInput();
            }
        }

        DB::transaction(function () use ($request, $transfert, $magasinId) {
            $transfert->update([
                'magasin_destination_id' => $request->magasin_destination_id,
                'date_transfert' => $request->date_transfert,
            ]);

            $transfert->lignes()->delete();

            foreach ($request->produits as $index => $produitId) {
                LigneTransfert::create([
                    'transfert_id' => $transfert->id,
                    'produit_id' => $produitId,
                    'quantite' => $request->quantites[$index],
                ]);
            }
        });

        return redirect()->route('module.transferts.index')->with('success', 'Transfert modifié avec succès.');
    }

    public function destroy(Transfert $transfert)
    {
        $magasinId = session('magasin_actif_id');

        if ($transfert->magasin_source_id !== $magasinId) {
            abort(403);
        }

        if ($transfert->statut !== 'en attente') {
            return redirect()->route('module.transferts.index')->with('error', 'Impossible de supprimer un transfert déjà validé ou refusé.');
        }

        $transfert->lignes()->delete();
        $transfert->delete();

        return back()->with('success', 'Transfert supprimé avec succès.');
    }

    public function valider(Request $request, Transfert $transfert)
    {
        $magasinId = session('magasin_actif_id');

        // Seul le magasin DESTINATION peut valider
        if ($transfert->magasin_destination_id != $magasinId) {
            abort(403);
        }

        if ($transfert->statut !== 'en attente') {
            return back()->with('error', 'Ce transfert a déjà été traité.');
        }

        DB::transaction(function () use ($transfert, $magasinId) {
            // Mise à jour du stock pour chaque produit transféré
            foreach ($transfert->lignes as $ligne) {
                $stock = \App\Models\Stock::firstOrCreate(
                    ['produit_id' => $ligne->produit_id, 'magasin_id' => $magasinId],
                    ['quantite' => 0]
                );

                $stock->increment('quantite', $ligne->quantite);
            }

            // Marquer comme validé
            $transfert->update(['statut' => 'valide']);
        });

        return redirect()->route('module.transferts.show', $transfert->id)
            ->with('success', 'Transfert reçu et stock mis à jour.');
    }

}
