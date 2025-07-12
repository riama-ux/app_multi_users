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
            // Crée l'enregistrement du transfert avec le statut 'attente'
            $transfert = Transfert::create([
                'magasin_source_id' => $magasinId,
                'magasin_destination_id' => $request->magasin_destination_id,
                'user_id' => auth()->id(),
                'date_transfert' => $request->date_transfert,
                'statut' => 'attente', // Le statut initial est 'attente'
            ]);

            // Crée les lignes de transfert associées
            foreach ($produitIds as $index => $produitId) {
                $quantiteATransferer = $quantitesDemandees[$index];

                LigneTransfert::create([
                    'transfert_id' => $transfert->id,
                    'produit_id' => $produitId, // ID du produit source
                    'quantite' => $quantiteATransferer,
                ]);
            }
            // IMPORTANT : Aucune modification de StockLot ou MouvementStock ici.
            // Ces opérations sont déplacées dans la méthode 'valider'.
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

        // Autorisation : Seul le magasin source peut modifier
        if ($transfert->magasin_source_id != $magasinId) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier ce transfert.');
        }

        // Vérifie si le transfert est encore en attente
        if ($transfert->statut !== 'attente') {
            return redirect()->route('transferts.index')->with('error', 'Impossible de modifier un transfert déjà validé ou refusé.');
        }

        $request->validate([
            'magasin_destination_id' => 'required|exists:magasins,id|different:magasin_source_id',
            'date_transfert' => 'required|date',
            'produits' => 'required|array|min:1',
            'quantites' => 'required|array|min:1',
        ]);

        // Nettoie les tableaux reçus du formulaire
        $produitIds = array_values(array_filter($request->produits, fn($value) => !is_null($value) && $value !== ''));
        $quantitesDemandees = array_values(array_filter($request->quantites, fn($value) => !is_null($value) && $value !== ''));

        if (count($produitIds) !== count($quantitesDemandees)) {
            return back()->with('error', 'Erreur de données : les quantités ne correspondent pas aux produits. Veuillez réessayer.')->withInput();
        }

        // Vérification de stock avant la transaction (identique à la méthode store)
        // Ceci s'assure que même après modification, le magasin source a toujours assez de stock.
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

        DB::transaction(function () use ($produitIds, $quantitesDemandees, $request, $transfert) {
            // Supprime les anciennes lignes de transfert
            // Aucune logique de "rollback" de stock n'est nécessaire ici car
            // les stocks ne sont pas affectés tant que le transfert est en statut 'attente'.
            $transfert->ligneTransferts()->delete();

            // Met à jour les détails du transfert
            $transfert->update([
                'magasin_destination_id' => $request->magasin_destination_id,
                'date_transfert' => $request->date_transfert,
            ]);

            // Ajoute les nouvelles lignes de transfert
            foreach ($produitIds as $index => $produitId) {
                $quantiteATransferer = $quantitesDemandees[$index];

                LigneTransfert::create([
                    'transfert_id' => $transfert->id,
                    'produit_id' => $produitId,
                    'quantite' => $quantiteATransferer,
                ]);
            }
            // IMPORTANT : Aucune modification de StockLot ou MouvementStock ici.
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
                        'marque' => $produitSource->marque,
                        'prix_achat' => $produitSource->prix_achat,
                        'cout_achat' => $produitSource->cout_achat,
                        'prix_vente' => $produitSource->prix_vente,
                        'marge' => $produitSource->marge,
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
