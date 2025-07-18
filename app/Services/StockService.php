<?php

namespace App\Services;

use App\Models\StockLot;
use App\Models\MouvementStock;
use Illuminate\Support\Facades\DB;
use App\Models\Produit;


class StockService
{
    /**
     * Sortie FIFO de stock (vente, transfert, etc.)
     *
     * @param int $produitId
     * @param int $quantiteTotale
     * @param string $sourceType (ex: 'vente')
     * @param int $sourceId (id de la vente)
     * @param int $magasinId
     * @param int|null $userId
     * @param string|null $motif
     * @return array tableau des lots utilisés [lot_id => quantité]
     * @throws \Exception
     */
    /*
    public static function sortirFifo($produitId, $quantiteTotale, $sourceType, $sourceId, $magasinId, $userId = null, $motif = null)
    {
        DB::beginTransaction();
        try {
            $quantiteRestante = $quantiteTotale;
            $lotsUtilises = [];

            $lots = StockLot::where('produit_id', $produitId)
                ->where('magasin_id', $magasinId)
                ->where('quantite', '>', 0)
                ->orderBy('date_reception', 'asc')
                ->lockForUpdate()
                ->get();

            foreach ($lots as $lot) {
                if ($quantiteRestante <= 0) break;

                $qteDisponible = $lot->quantite;
                $qtePrelevee = min($quantiteRestante, $qteDisponible);

                // Mise à jour du lot
                $lot->quantite -= $qtePrelevee;
                $lot->save();
                self::mettreAJourStock($produitId, $magasinId);

                // Mouvement stock
                MouvementStock::create([
                    'produit_id' => $produitId,
                    'type' => 'sortie',
                    'quantite' => $qtePrelevee,
                    'source_type' => $sourceType,
                    'source_id' => $sourceId,
                    'lot_id' => $lot->id,
                    'magasin_id' => $magasinId,
                    'user_id' => $userId,
                    'motif' => $motif,
                    'date' => now(),
                ]);

                $lotsUtilises[$lot->id] = $qtePrelevee;
                $quantiteRestante -= $qtePrelevee;
            }

            if ($quantiteRestante > 0) {
                throw new \Exception("Stock insuffisant pour le produit ID {$produitId}");
            }

            DB::commit();
            return $lotsUtilises;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    */


    public static function sortirFifo($produitId, $quantiteTotale, $sourceType, $sourceId, $magasinId, $userId = null, $motif = null)
{
    DB::beginTransaction();
    try {
        $quantiteRestante = $quantiteTotale;
        $lotsUtilises = [];

        $lots = StockLot::where('produit_id', $produitId)
            ->where('magasin_id', $magasinId)
            ->where('quantite', '>', 0)
            ->orderBy('date_reception', 'asc')
            ->lockForUpdate()
            ->get();

        foreach ($lots as $lot) {
            if ($quantiteRestante <= 0) break;

            $qteDisponible = $lot->quantite;
            $qtePrelevee = min($quantiteRestante, $qteDisponible);

            // Mise à jour du lot
            $lot->quantite -= $qtePrelevee;
            $lot->save();

            // Mouvement stock
            MouvementStock::create([
                'produit_id' => $produitId,
                'type' => 'sortie',
                'quantite' => $qtePrelevee,
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'lot_id' => $lot->id,
                'magasin_id' => $magasinId,
                'user_id' => $userId,
                'motif' => $motif,
                'date' => now(),
            ]);

            $lotsUtilises[$lot->id] = $qtePrelevee;
            $quantiteRestante -= $qtePrelevee;
        }

        if ($quantiteRestante > 0) {
            throw new \Exception("Stock insuffisant pour le produit ID {$produitId}");
        }

        // Mise à jour directe du stock global ici, après avoir modifié les lots
        $produit = Produit::find($produitId);
            if ($produit) {
                // Assurez-vous que cette méthode existe dans votre modèle Produit
                // et qu'elle calcule la somme des quantités de tous les StockLot pour ce produit.
                $produit->updateQuantiteFromLots();
            } else {
                // Gérer le cas où le produit n'est pas trouvé (bien que cela soit peu probable si $produitId est valide)
                throw new \Exception("Produit avec l'ID {$produitId} introuvable lors de la mise à jour du stock global.");
            }

        DB::commit();

        return $lotsUtilises;
    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
}




    public static function mettreAJourStock($produit_id, $magasin_id)
    {
        $quantite = StockLot::where('produit_id', $produit_id)
            ->where('magasin_id', $magasin_id)
            ->sum('quantite');

        $stock = \App\Models\Stock::firstOrCreate([
            'produit_id' => $produit_id,
            'magasin_id' => $magasin_id,
        ]);

        $stock->update(['quantite' => $quantite]);
    }

    public static function reintegrerStockLot(
        int $produitId,
        float $quantiteAReintegrer,
        int $magasinId,
        int $userId,
        string $motif,
        string $sourceType,
        int $sourceId,
        ?int $lotId = null, // Peut être null
        float $coutAchat = 0 // Coût d'achat pour le nouveau lot si créé
    ): void {
        if ($quantiteAReintegrer <= 0) {
            throw new Exception("La quantité à réintégrer doit être supérieure à zéro.");
        }

        DB::beginTransaction();
        try {
            $lot = null;

            // Si un lotId est fourni, tenter de le trouver
            if ($lotId) {
                $lot = StockLot::where('id', $lotId)
                               ->where('produit_id', $produitId)
                               ->where('magasin_id', $magasinId)
                               ->lockForUpdate() // Verrouille le lot pour la transaction
                               ->first(); // Utiliser first() au lieu de firstOrFail() pour éviter l'erreur si non trouvé
            }

            // Si le lot est trouvé, ou si un nouveau lot doit être créé
            if ($lot) {
                // Si le lot existant est trouvé, ajouter la quantité retournée
                $lot->quantite += $quantiteAReintegrer;
                $lot->save();
            } else {
                // Si aucun lotId n'est fourni, ou si le lotId fourni n'existe pas,
                // créer un nouveau lot pour le stock retourné.
                $lot = StockLot::create([
                    'produit_id' => $produitId,
                    'magasin_id' => $magasinId,
                    'quantite' => $quantiteAReintegrer,
                    'quantite_restante' => $quantiteAReintegrer,
                    'cout_achat' => $coutAchat, // Utilise le coutAchat fourni pour le nouveau lot
                    'date_reception' => now(),
                ]);
            }

            // Enregistre le mouvement de stock (entrée) pour le retour
            MouvementStock::create([
                'produit_id' => $produitId,
                'magasin_id' => $magasinId,
                'type' => 'entree', // Un retour est une entrée de stock
                'quantite' => $quantiteAReintegrer,
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'user_id' => $userId,
                'motif' => $motif,
                'date' => now(),
                'lot_id' => $lot->id, // Lie le mouvement au lot qui a reçu le stock
            ]);

            // Met à jour la quantité agrégée du produit dans le modèle Produit
            Produit::find($produitId)->updateQuantiteFromLots();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Erreur lors de la réintégration de stock: " . $e->getMessage());
        }
    }


    public static function entrerStock(
        int $produitId,
        float $quantite,
        string $sourceType,
        ?int $sourceId,
        int $magasinId,
        int $userId,
        string $description,
        float $coutUnitaire
    ) {
        DB::transaction(function () use ($produitId, $quantite, $sourceType, $sourceId, $magasinId, $userId, $description, $coutUnitaire) {
            $produit = Produit::where('id', $produitId)
                              ->where('magasin_id', $magasinId)
                              ->firstOrFail();

            // Quantité du produit avant ce mouvement
            $quantiteAvantProduit = $produit->quantite;

            // Créer un nouveau lot pour l'entrée de stock
            $lot = StockLot::create([
                'produit_id' => $produit->id,
                'magasin_id' => $magasinId,
                'quantite' => $quantite,
                'quantite_restante' => $quantite, // Initialement, la quantité restante est égale à la quantité totale du lot
                'cout_achat' => $coutUnitaire,
                'date_reception' => now(),
                'source_type' => $sourceType,
                'source_id' => $sourceId,
            ]);

            // Mettre à jour la quantité agrégée du produit via la somme des lots
            $produit->updateQuantiteFromLots();

            // Enregistrer le mouvement de stock
            MouvementStock::create([
                'produit_id' => $produit->id,
                'magasin_id' => $magasinId,
                'type' => 'entree',
                'quantite' => $quantite,
                'quantite_avant' => $quantiteAvantProduit,
                'quantite_apres' => $produit->quantite, // Après l'incrémentation par updateQuantiteFromLots
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'lot_id' => $lot->id,
                'user_id' => $userId,
                'motif' => $description,
                'date' => now(),
            ]);
        });
    }

}
