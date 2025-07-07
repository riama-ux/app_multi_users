<?php

namespace App\Http\Controllers\Gestion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transfert;
use App\Models\LigneTransfert;
use App\Models\Produit;
use App\Models\Categorie;
use App\Models\StockLot;
use App\Models\MouvementStock;
use App\Services\StockService;
use App\Models\Stock;
use App\Models\Magasin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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

        return view('transferts.index', compact('transferts'));
    }

    public function create()
    {
        $magasinId = session('magasin_actif_id');

        $magasins = Magasin::where('id', '!=', $magasinId)->get(); // pour choisir la destination (différente de source)
        $produits = Produit::whereHas('stocks', function ($q) use ($magasinId) {
            $q->where('magasin_id', $magasinId)->where('quantite', '>', 0);
        })->get();

        return view('transferts.create', compact('magasins', 'produits'));
    }

    public function store(Request $request)
    {
        $magasinId = session('magasin_actif_id');

        $request->validate([
            'magasin_destination_id' => 'required|exists:magasins,id',
            'produits' => 'required|array|min:1',
            'quantites' => 'required|array|min:1',
        ]);
        if ($request->magasin_destination_id == $magasinId) {
            return back()->with('error', 'Le magasin de destination doit être différent du magasin source.')->withInput();
        }

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
                'date_transfert' => now(),
                'statut' => 'attente',
            ]);

            foreach ($request->produits as $index => $produitId) {
                LigneTransfert::create([
                    'transfert_id' => $transfert->id,
                    'produit_id' => $produitId,
                    'quantite' => $request->quantites[$index],
                ]);
            }
        });

        return redirect()->route('transferts.index')->with('success', 'Transfert créé avec succès.');
    }

    public function show(Transfert $transfert)
    {
        $magasinId = session('magasin_actif_id');

        if (auth()->user()->role !== 'Admin' && $transfert->magasin_source_id !== $magasinId && $transfert->magasin_destination_id !== $magasinId) {
            abort(403);
        }


        $transfert->load('ligneTransferts.produit', 'magasinSource', 'magasinDestination', 'user');

        return view('transferts.show', compact('transfert'));
    }

    public function edit(Transfert $transfert)
    {
        $magasinId = session('magasin_actif_id');


        if ($transfert->magasin_source_id != $magasinId) {
            abort(403); // Seul magasin source peut éditer
        }

        if ($transfert->statut !== 'attente') {
            return redirect()->route('transferts.index')->with('error', 'Impossible de modifier un transfert déjà validé ou refusé.');
        }

        $magasins = Magasin::where('id', '!=', $magasinId)->get();
        $produits = Produit::whereHas('stocks', function ($q) use ($magasinId) {
            $q->where('magasin_id', $magasinId);
        })->get();

        $transfert->load('ligneTransferts');

        return view('transferts.edit', compact('transfert', 'magasins', 'produits'));
    }

    public function update(Request $request, Transfert $transfert)
    {
        $magasinId = session('magasin_actif_id');

        if ($transfert->magasin_source_id != $magasinId) {
            abort(403);
        }

        if ($transfert->statut !== 'attente') {
            return redirect()->route('transferts.index')->with('error', 'Impossible de modifier un transfert déjà validé ou refusé.');
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

            $transfert->ligneTransferts()->delete();

            foreach ($request->produits as $index => $produitId) {
                LigneTransfert::create([
                    'transfert_id' => $transfert->id,
                    'produit_id' => $produitId,
                    'quantite' => $request->quantites[$index],
                ]);
            }
        });

        return redirect()->route('transferts.index')->with('success', 'Transfert modifié avec succès.');
    }

    public function destroy(Transfert $transfert)
    {
        $magasinId = session('magasin_actif_id');

        if ($transfert->magasin_source_id != $magasinId) {
            abort(403);
        }

        if ($transfert->statut !== 'attente') {
            return redirect()->route('transferts.index')->with('error', 'Impossible de supprimer un transfert déjà validé ou refusé.');
        }

        $transfert->ligneTransferts()->delete();
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

        if ($transfert->statut !== 'attente') {
            return back()->with('error', 'Ce transfert a déjà été traité.');
        }

        DB::transaction(function () use ($transfert, $magasinId) {
            $sourceMagasinId = $transfert->magasin_source_id;
            $userId = auth()->id();

            foreach ($transfert->ligneTransferts as $ligne) {
                $produitId = $ligne->produit_id;
                $quantiteDemandee = $ligne->quantite;

                $produitSource = \App\Models\Produit::where('id', $produitId)
                    ->where('magasin_id', $sourceMagasinId)
                    ->first();

                if (!$produitSource) continue;

                // ➤ Trouver la catégorie source
                $categorieSource = \App\Models\Categorie::where('id', $produitSource->categorie_id)
                    ->where('magasin_id', $sourceMagasinId)
                    ->first();

                // ➤ Créer ou retrouver la catégorie destination
                $categorieDestination = \App\Models\Categorie::firstOrCreate(
                    ['nom' => $categorieSource->nom, 'magasin_id' => $magasinId],
                    ['created_at' => now(), 'updated_at' => now()]
                );

                // ➤ Vérifier si produit existe déjà dans le magasin destination
                $produitDestination = \App\Models\Produit::where('reference', $produitSource->reference)
                    ->where('magasin_id', $magasinId)
                    ->first();

                if (!$produitDestination) {
                    $produitDestination = \App\Models\Produit::create([
                        'nom' => $produitSource->nom,
                        'categorie_id' => $categorieDestination->id,
                        'magasin_id' => $magasinId,
                        'reference' => $produitSource->reference,
                        'prix_achat' => $produitSource->prix_achat,
                        'cout_achat' => $produitSource->cout_achat,
                        'prix_vente' => $produitSource->prix_vente,
                        'description' => $produitSource->description,
                        'seuil_alerte' => $produitSource->seuil_alerte,
                    ]);
                }

                // ➤ Décrémenter les lots FIFO du magasin source
                $quantiteRestante = $quantiteDemandee;

                $lotsSource = \App\Models\StockLot::where('produit_id', $produitId)
                    ->where('magasin_id', $sourceMagasinId)
                    ->where('quantite', '>', 0)
                    ->orderBy('date_reception')
                    ->lockForUpdate()
                    ->get();

                foreach ($lotsSource as $lot) {
                    if ($quantiteRestante <= 0) break;

                    $aRetirer = min($lot->quantite, $quantiteRestante);
                    $lot->quantite -= $aRetirer;
                    $lot->save();

                    //  Mouvement SORTIE
                    \App\Models\MouvementStock::create([
                        'produit_id' => $produitId,
                        'magasin_id' => $sourceMagasinId,
                        'type' => 'sortie',
                        'quantite' => $aRetirer,
                        'source_type' => 'transfert',
                        'source_id' => $transfert->id,
                        'user_id' => $userId,
                        'motif' => 'Transfert vers magasin ID ' . $magasinId,
                        'date' => now(),
                        'lot_id' => $lot->id,
                    ]);

                    // Créer lot correspondant dans destination
                    $nouveauLot = \App\Models\StockLot::create([
                        'produit_id' => $produitDestination->id,
                        'magasin_id' => $magasinId,
                        'quantite' => $aRetirer,
                        'quantite_restante' => $aRetirer,
                        'cout_achat' => $lot->cout_achat,
                        'date_reception' => now(),
                    ]);

                    // Mouvement ENTREE
                    \App\Models\MouvementStock::create([
                        'produit_id' => $produitDestination->id,
                        'magasin_id' => $magasinId,
                        'type' => 'entree',
                        'quantite' => $aRetirer,
                        'source_type' => 'transfert',
                        'source_id' => $transfert->id,
                        'user_id' => $userId,
                        'motif' => 'Réception de transfert depuis magasin ID ' . $sourceMagasinId,
                        'date' => now(),
                        'lot_id' => $nouveauLot->id,
                    ]);

                    $quantiteRestante -= $aRetirer;
                }
                if ($quantiteRestante > 0) {
                    throw new \Exception("Stock insuffisant dans les lots pour le produit ID {$produitId}");
                }

                // ➤ Mettre à jour stock global (table stocks)
                $stock = \App\Models\Stock::firstOrCreate(
                    ['produit_id' => $produitDestination->id, 'magasin_id' => $magasinId],
                    ['quantite' => 0]
                );
                $stock->increment('quantite', $quantiteDemandee);


                \App\Models\Stock::where('produit_id', $produitId)
                    ->where('magasin_id', $sourceMagasinId)
                    ->decrement('quantite', $quantiteDemandee);
            }

            // Marquer comme validé
            $transfert->update(['statut' => 'envoye']);
        });

        return redirect()->route('transferts.show', $transfert->id)
            ->with('success', 'Transfert validé avec gestion des lots et mouvements de stock.');
    }



    /*
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
            $sourceMagasinId = $transfert->magasin_source_id;

            foreach ($transfert->lignes as $ligne) {
                $produitSource = \App\Models\Produit::where('id', $ligne->produit_id)
                    ->where('magasin_id', $sourceMagasinId)
                    ->first();

                if (!$produitSource) {
                    continue; // au cas où le produit n'existe plus
                }

                // ✅ Vérifier si le produit existe déjà dans le magasin destination
                $produitDestination = \App\Models\Produit::where('code', $produitSource->code)
                    ->where('magasin_id', $magasinId)
                    ->first();

                // ✅ S'il n'existe pas, on le crée
                if (!$produitDestination) {
                    $produitDestination = \App\Models\Produit::create([
                        'nom' => $produitSource->nom,
                        'categorie_id' => $produitSource->categorie_id,
                        'magasin_id' => $magasinId,
                        'code' => $produitSource->code,
                        'prix_achat' => $produitSource->prix_achat,
                        'cout_achat' => $produitSource->cout_achat,
                        'prix_vente' => $produitSource->prix_vente,
                        'description' => $produitSource->description,
                    ]);
                }

                // ✅ Décrémenter le stock dans le magasin source
                $stockSource = \App\Models\Stock::where([
                    'produit_id' => $ligne->produit_id,
                    'magasin_id' => $sourceMagasinId
                ])->first();

                if ($stockSource) {
                    $stockSource->decrement('quantite', $ligne->quantite);
                }

                // ✅ Incrémenter le stock dans le magasin destination
                $stockDestination = \App\Models\Stock::firstOrCreate(
                    ['produit_id' => $produitDestination->id, 'magasin_id' => $magasinId],
                    ['quantite' => 0]
                );

                $stockDestination->increment('quantite', $ligne->quantite);
            }

            // Marquer comme validé
            $transfert->update(['statut' => 'valide']);
        });

        return redirect()->route('module.transferts.show', $transfert->id)
            ->with('success', 'Transfert reçu, produit copié et stock mis à jour.');
    }
    */
}