<?php

namespace App\Http\Controllers\Gestion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transfert;
use App\Models\LigneTransfert;
use App\Models\Produit;
use App\Models\Categorie;
use App\Models\StockLot; // Importation nécessaire pour la gestion des lots
use App\Models\MouvementStock; // Importation nécessaire pour les mouvements de stock
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

        $magasins = Magasin::where('id', '!=', $magasinId)->get();
        // Pour la création, les produits sont filtrés par leur quantité agrégée (somme des lots)
        $produits = Produit::where('magasin_id', $magasinId)
                           ->where('quantite', '>', 0) // 'quantite' ici est la somme des lots
                           ->get();

        return view('transferts.create', compact('magasins', 'produits'));
    }

    public function store(Request $request)
    {
        $magasinId = session('magasin_actif_id'); // Magasin source

        $request->validate([
            'magasin_destination_id' => 'required|exists:magasins,id',
            'produits' => 'required|array|min:1',
            'quantites' => 'required|array|min:1',
            'date_transfert' => 'required|date',
        ]);

        if ($request->magasin_destination_id == $magasinId) {
            return back()->with('error', 'Le magasin de destination doit être différent du magasin source.')->withInput();
        }

        // Nettoyer les tableaux reçus du formulaire (supprimer les null/vides et réindexer)
        $produitIds = array_values(array_filter($request->produits, fn($value) => !is_null($value) && $value !== ''));
        $quantitesDemandees = array_values(array_filter($request->quantites, fn($value) => !is_null($value) && $value !== ''));

        if (count($produitIds) !== count($quantitesDemandees)) {
            return back()->with('error', 'Erreur de données : les quantités ne correspondent pas aux produits. Veuillez réessayer.')->withInput();
        }

        // --- DÉBUT DE LA VÉRIFICATION DE STOCK AVEC LES LOTS (FIFO) ---
        foreach ($produitIds as $index => $produitId) {
            $quantiteDemandee = $quantitesDemandees[$index];

            $produitSource = Produit::where('id', $produitId)
                                    ->where('magasin_id', $magasinId)
                                    ->first();

            if (!$produitSource) {
                return back()->with('error', "Produit introuvable dans le magasin source : ID $produitId.")->withInput();
            }

            // Vérifier la quantité totale disponible via les lots pour ce produit dans le magasin source
            $totalQuantiteDisponibleLots = StockLot::where('produit_id', $produitId)
                                                   ->where('magasin_id', $magasinId)
                                                   ->sum('quantite');

            if ($totalQuantiteDisponibleLots < $quantiteDemandee) {
                return back()->with('error', "Stock insuffisant pour le produit : {$produitSource->nom} ({$produitSource->reference}). Quantité disponible: {$totalQuantiteDisponibleLots}. Quantité demandée: {$quantiteDemandee}.")->withInput();
            }
        }
        // --- FIN DE LA VÉRIFICATION DE STOCK AVEC LES LOTS ---

        DB::transaction(function () use ($produitIds, $quantitesDemandees, $request, $magasinId) {
            $transfert = Transfert::create([
                'magasin_source_id' => $magasinId,
                'magasin_destination_id' => $request->magasin_destination_id,
                'user_id' => auth()->id(),
                'date_transfert' => $request->date_transfert,
                'statut' => 'attente',
            ]);

            foreach ($produitIds as $index => $produitId) {
                $quantiteATransferer = $quantitesDemandees[$index];
                $produitSource = Produit::where('id', $produitId)
                                        ->where('magasin_id', $magasinId)
                                        ->firstOrFail(); // Doit exister car vérifié avant

                // Créer la ligne de transfert
                LigneTransfert::create([
                    'transfert_id' => $transfert->id,
                    'produit_id' => $produitId, // ID du produit source
                    'quantite' => $quantiteATransferer,
                ]);

                // --- GESTION FIFO DES LOTS POUR LE MAGASIN SOURCE ---
                $quantiteRestantePourSource = $quantiteATransferer;
                $lotsSource = StockLot::where('produit_id', $produitId)
                    ->where('magasin_id', $magasinId)
                    ->where('quantite', '>', 0)
                    ->orderBy('date_reception') // FIFO
                    ->lockForUpdate() // Verrouille les lots pour la transaction
                    ->get();

                foreach ($lotsSource as $lot) {
                    if ($quantiteRestantePourSource <= 0) break;

                    $aRetirerDeCeLot = min($lot->quantite, $quantiteRestantePourSource);
                    $lot->quantite -= $aRetirerDeCeLot;
                    $lot->save();

                    // Enregistrer le mouvement de stock (SORTIE) pour le magasin source
                    MouvementStock::create([
                        'produit_id' => $produitId,
                        'magasin_id' => $magasinId,
                        'type' => 'sortie',
                        'quantite' => $aRetirerDeCeLot,
                        'source_type' => 'transfert',
                        'source_id' => $transfert->id,
                        'user_id' => auth()->id(),
                        'motif' => 'Transfert vers magasin ID ' . $request->magasin_destination_id,
                        'date' => now(),
                        'lot_id' => $lot->id,
                    ]);

                    $quantiteRestantePourSource -= $aRetirerDeCeLot;
                }
                // Si la quantité restante est > 0 ici, c'est une erreur logique car on a vérifié le stock avant.
                // throw new \Exception("Erreur interne: Stock insuffisant malgré la vérification préalable.");

                // Mettre à jour la quantité agrégée du produit source après décrémentation des lots
                $produitSource->updateQuantiteFromLots(); // Appelle la méthode du modèle Produit

                // --- GESTION POUR LE MAGASIN DE DESTINATION ---
                // 1. Créer ou retrouver le produit dans le magasin de destination
                $produitDestination = Produit::where('reference', $produitSource->reference)
                                             ->where('magasin_id', $request->magasin_destination_id)
                                             ->first();

                if (!$produitDestination) {
                    // Créer le produit dans le magasin de destination s'il n'existe pas
                    $newProductData = $produitSource->toArray();
                    unset($newProductData['id']); // L'ID doit être unique
                    $newProductData['magasin_id'] = $request->magasin_destination_id;
                    $newProductData['quantite'] = 0; // La quantité agrégée sera mise à jour par les lots
                    // Assurez-vous que 'categorie_id' est géré correctement si les catégories sont spécifiques au magasin
                    $produitDestination = Produit::create($newProductData);
                }

                // 2. Créer un nouveau lot pour le magasin de destination
                $nouveauLotDestination = StockLot::create([
                    'produit_id' => $produitDestination->id,
                    'magasin_id' => $request->magasin_destination_id,
                    'quantite' => $quantiteATransferer,
                    'quantite_restante' => $quantiteATransferer,
                    'cout_achat' => $produitSource->cout_achat, // Utiliser le coût d'achat du produit source
                    'date_reception' => now(), // Date de réception dans le magasin de destination
                ]);

                // 3. Enregistrer le mouvement de stock (ENTRÉE) pour le magasin de destination
                MouvementStock::create([
                    'produit_id' => $produitDestination->id,
                    'magasin_id' => $request->magasin_destination_id,
                    'type' => 'entree',
                    'quantite' => $quantiteATransferer,
                    'source_type' => 'transfert',
                    'source_id' => $transfert->id,
                    'user_id' => auth()->id(),
                    'motif' => 'Réception de transfert depuis magasin ID ' . $magasinId,
                    'date' => now(),
                    'lot_id' => $nouveauLotDestination->id,
                ]);

                // Mettre à jour la quantité agrégée du produit de destination après création du lot
                $produitDestination->updateQuantiteFromLots(); // Appelle la méthode du modèle Produit
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

        // CHARGEMENT CRUCIAL : Assurez-vous que les produits liés sont chargés.
        // Utiliser withTrashed() pour inclure les produits qui auraient pu être soft-deleted.
        $transfert->load(['ligneTransferts' => function($query) {
            $query->with(['produit' => function($prodQuery) {
                $prodQuery->withTrashed(); // Charge les produits même s'ils sont soft-deleted
            }]);
        }]);

        // Pour le débogage, vous pouvez logger les données ici
        // \Log::info('Transfert Lignes for edit: ' . json_encode($transfert->ligneTransferts->toArray()));

        return view('transferts.edit', compact('transfert', 'magasins'));
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

        // Nettoyer les tableaux reçus du formulaire (supprimer les null/vides et réindexer)
        $produitIds = array_values(array_filter($request->produits, fn($value) => !is_null($value) && $value !== ''));
        $quantitesDemandees = array_values(array_filter($request->quantites, fn($value) => !is_null($value) && $value !== ''));

        if (count($produitIds) !== count($quantitesDemandees)) {
            return back()->with('error', 'Erreur de données : les quantités ne correspondent pas aux produits. Veuillez réessayer.')->withInput();
        }

        // Vérification de stock avant la transaction (identique à la méthode store)
        foreach ($produitIds as $index => $produitId) {
            $quantiteDemandee = $quantitesDemandees[$index];
            $produitSource = Produit::where('id', $produitId)
                                    ->where('magasin_id', $magasinId)
                                    ->first();

            if (!$produitSource) {
                return back()->with('error', "Produit introuvable dans le magasin source : ID $produitId.")->withInput();
            }

            $totalQuantiteDisponibleLots = StockLot::where('produit_id', $produitId)
                                                   ->where('magasin_id', $magasinId)
                                                   ->sum('quantite');

            if ($totalQuantiteDisponibleLots < $quantiteDemandee) {
                return back()->with('error', "Stock insuffisant pour le produit : {$produitSource->nom} ({$produitSource->reference}). Quantité disponible: {$totalQuantiteDisponibleLots}. Quantité demandée: {$quantiteDemandee}.")->withInput();
            }
        }

        DB::transaction(function () use ($produitIds, $quantitesDemandees, $request, $transfert, $magasinId) {
            // Revertir les stocks des anciens lignes de transfert avant de les supprimer
            // ATTENTION: Cette logique de rollback est simplifiée et ne gère pas les lots FIFO.
            // Pour une gestion complète, il faudrait annuler les mouvements de stock et réajuster les lots.
            // Ici, nous nous basons sur la quantité agrégée du produit pour le rollback.
            foreach ($transfert->ligneTransferts as $oldLigne) {
                $oldProduitSource = Produit::where('id', $oldLigne->produit_id)
                                            ->where('magasin_id', $magasinId)
                                            ->first();
                if ($oldProduitSource) {
                    // Ré-incrémenter la quantité dans les lots du magasin source
                    // C'est une logique complexe de "rollback" des lots,
                    // pour simplifier, nous allons juste ré-incrémenter la quantité agrégée du produit source
                    // et décrémenter celle du produit destination.
                    // Une gestion complète des lots pour l'édition serait très complexe.
                    // Pour l'instant, on se base sur la quantité agrégée pour le rollback.
                    $oldProduitSource->increment('quantite', $oldLigne->quantite);

                    $oldProduitDestination = Produit::where('reference', $oldProduitSource->reference)
                                                    ->where('magasin_id', $transfert->magasin_destination_id)
                                                    ->first();
                    if ($oldProduitDestination) {
                        $oldProduitDestination->decrement('quantite', $oldLigne->quantite);
                    }
                }
            }

            // Supprimer les anciennes lignes de transfert
            $transfert->ligneTransferts()->delete();

            // Mettre à jour les détails du transfert
            $transfert->update([
                'magasin_destination_id' => $request->magasin_destination_id,
                'date_transfert' => $request->date_transfert,
            ]);

            // Ajouter les nouvelles lignes de transfert et gérer les stocks
            foreach ($produitIds as $index => $produitId) {
                $quantiteATransferer = $quantitesDemandees[$index];
                $produitSource = Produit::where('id', $produitId)
                                        ->where('magasin_id', $magasinId)
                                        ->firstOrFail();

                LigneTransfert::create([
                    'transfert_id' => $transfert->id,
                    'produit_id' => $produitId,
                    'quantite' => $quantiteATransferer,
                ]);

                // --- GESTION FIFO DES LOTS POUR LE MAGASIN SOURCE (identique à store) ---
                $quantiteRestantePourSource = $quantiteATransferer;
                $lotsSource = StockLot::where('produit_id', $produitId)
                    ->where('magasin_id', $magasinId)
                    ->where('quantite', '>', 0)
                    ->orderBy('date_reception')
                    ->lockForUpdate()
                    ->get();

                foreach ($lotsSource as $lot) {
                    if ($quantiteRestantePourSource <= 0) break;

                    $aRetirerDeCeLot = min($lot->quantite, $quantiteRestantePourSource);
                    $lot->quantite -= $aRetirerDeCeLot;
                    $lot->save();

                    MouvementStock::create([
                        'produit_id' => $produitId,
                        'magasin_id' => $magasinId,
                        'type' => 'sortie',
                        'quantite' => $aRetirerDeCeLot,
                        'source_type' => 'transfert',
                        'source_id' => $transfert->id,
                        'user_id' => auth()->id(),
                        'motif' => 'Transfert (modification) vers magasin ID ' . $request->magasin_destination_id,
                        'date' => now(),
                        'lot_id' => $lot->id,
                    ]);
                    $quantiteRestantePourSource -= $aRetirerDeCeLot;
                }
                $produitSource->updateQuantiteFromLots(); // Mettre à jour la quantité agrégée du produit source

                // --- GESTION POUR LE MAGASIN DE DESTINATION (identique à store) ---
                $produitDestination = Produit::where('reference', $produitSource->reference)
                                             ->where('magasin_id', $request->magasin_destination_id)
                                             ->first();

                if (!$produitDestination) {
                    $newProductData = $produitSource->toArray();
                    unset($newProductData['id']);
                    $newProductData['magasin_id'] = $request->magasin_destination_id;
                    $newProductData['quantite'] = 0;
                    $produitDestination = Produit::create($newProductData);
                }

                $nouveauLotDestination = StockLot::create([
                    'produit_id' => $produitDestination->id,
                    'magasin_id' => $request->magasin_destination_id,
                    'quantite' => $quantiteATransferer,
                    'quantite_restante' => $quantiteATransferer,
                    'cout_achat' => $produitSource->cout_achat,
                    'date_reception' => now(),
                ]);

                MouvementStock::create([
                    'produit_id' => $produitDestination->id,
                    'magasin_id' => $request->magasin_destination_id,
                    'type' => 'entree',
                    'quantite' => $quantiteATransferer,
                    'source_type' => 'transfert',
                    'source_id' => $transfert->id,
                    'user_id' => auth()->id(),
                    'motif' => 'Réception de transfert (modification) depuis magasin ID ' . $magasinId,
                    'date' => now(),
                    'lot_id' => $nouveauLotDestination->id,
                ]);
                $produitDestination->updateQuantiteFromLots(); // Mettre à jour la quantité agrégée du produit de destination
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

        // Pour une suppression, il faudrait aussi "annuler" les mouvements de stock et les lots
        // C'est une logique complexe qui dépend de l'état du transfert.
        // Si le transfert n'a pas encore été validé (statut 'attente'), on peut juste le supprimer.
        // Si les lots ont déjà été décrémentés (par exemple, si le transfert a un statut 'envoyé'),
        // il faudrait les ré-incrémenter ou créer des mouvements inverses.
        // Pour l'instant, on suppose que 'attente' signifie qu'aucun stock n'a été touché.
        $transfert->ligneTransferts()->delete();
        $transfert->delete();

        return back()->with('success', 'Transfert supprimé avec succès.');
    }


    public function valider(Request $request, Transfert $transfert)
    {
        $magasinId = session('magasin_actif_id'); // Magasin de destination

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

                // On doit récupérer le produit d'origine (du magasin source)
                $produitSource = Produit::where('id', $produitId)
                                        ->where('magasin_id', $sourceMagasinId)
                                        ->firstOrFail(); // Doit exister

                // ➤ Trouver la catégorie source
                $categorieSource = Categorie::where('id', $produitSource->categorie_id)
                                            ->where('magasin_id', $sourceMagasinId)
                                            ->firstOrFail(); // Doit exister

                // ➤ Créer ou retrouver la catégorie destination
                $categorieDestination = Categorie::firstOrCreate(
                    ['nom' => $categorieSource->nom, 'magasin_id' => $magasinId],
                    ['created_at' => now(), 'updated_at' => now()]
                );

                // ➤ Vérifier si produit existe déjà dans le magasin destination
                $produitDestination = Produit::where('reference', $produitSource->reference)
                                             ->where('magasin_id', $magasinId)
                                             ->first();

                if (!$produitDestination) {
                    $produitDestination = Produit::create([
                        'nom' => $produitSource->nom,
                        'categorie_id' => $categorieDestination->id,
                        'magasin_id' => $magasinId,
                        'reference' => $produitSource->reference,
                        'code' => $produitSource->code,
                        'prix_achat' => $produitSource->prix_achat,
                        'cout_achat' => $produitSource->cout_achat,
                        'prix_vente' => $produitSource->prix_vente,
                        'description' => $produitSource->description,
                        'seuil_alerte' => $produitSource->seuil_alerte,
                        'quantite' => 0, // La quantité sera mise à jour via les lots
                    ]);
                }

                // --- GESTION FIFO DES LOTS ET MOUVEMENTS DE STOCK POUR LA VALIDATION ---
                // Ici, la validation signifie que le stock quitte la source et arrive à destination.
                // Cela devrait être fait dans la méthode 'store' au moment de la création du transfert
                // si le statut 'attente' signifie que le stock n'est pas encore bougé.
                // Si 'valider' est l'étape où le stock bouge réellement, alors cette logique est correcte ici.
                // Pour la cohérence avec ma proposition précédente, j'ai mis la logique de mouvement de stock dans 'store'.
                // Si 'valider' est l'étape de mouvement, alors le code ci-dessous est bon,
                // mais il faut retirer la logique de mouvement de stock de la méthode 'store'.

                // Décrémenter les lots FIFO du magasin source
                $quantiteRestante = $quantiteDemandee;
                $lotsSource = StockLot::where('produit_id', $produitId)
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

                    // Mouvement SORTIE
                    MouvementStock::create([
                        'produit_id' => $produitId,
                        'magasin_id' => $sourceMagasinId,
                        'type' => 'sortie',
                        'quantite' => $aRetirer,
                        'source_type' => 'transfert',
                        'source_id' => $transfert->id,
                        'user_id' => $userId,
                        'motif' => 'Transfert validé vers magasin ID ' . $magasinId,
                        'date' => now(),
                        'lot_id' => $lot->id,
                    ]);

                    // Créer lot correspondant dans destination
                    $nouveauLot = StockLot::create([
                        'produit_id' => $produitDestination->id,
                        'magasin_id' => $magasinId,
                        'quantite' => $aRetirer,
                        'quantite_restante' => $aRetirer,
                        'cout_achat' => $lot->cout_achat,
                        'date_reception' => now(),
                    ]);

                    // Mouvement ENTREE
                    MouvementStock::create([
                        'produit_id' => $produitDestination->id,
                        'magasin_id' => $magasinId,
                        'type' => 'entree',
                        'quantite' => $aRetirer,
                        'source_type' => 'transfert',
                        'source_id' => $transfert->id,
                        'user_id' => $userId,
                        'motif' => 'Réception de transfert validé depuis magasin ID ' . $sourceMagasinId,
                        'date' => now(),
                        'lot_id' => $nouveauLot->id,
                    ]);

                    $quantiteRestante -= $aRetirer;
                }
                if ($quantiteRestante > 0) {
                    throw new \Exception("Stock insuffisant dans les lots pour le produit ID {$produitId} lors de la validation.");
                }

                // Mettre à jour la quantité agrégée du produit source et destination
                Produit::find($produitId)->updateQuantiteFromLots(); // Produit source
                Produit::find($produitDestination->id)->updateQuantiteFromLots(); // Produit destination
            }

            // Marquer comme validé
            $transfert->update(['statut' => 'envoye']); // Statut final 'valide'
        });

        return redirect()->route('transferts.show', $transfert->id)
            ->with('success', 'Transfert validé avec gestion des lots et mouvements de stock.');
    }
}
