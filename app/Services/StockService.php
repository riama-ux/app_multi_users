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

    public static function reintegrerStockLot($lotId, $quantite, $magasinId, $userId = null, $motif = null, $sourceType = 'correction', $sourceId = null)
    {
        DB::beginTransaction();
        try {
            $lot = StockLot::where('id', $lotId)
                ->where('magasin_id', $magasinId)
                ->lockForUpdate()
                ->firstOrFail();

            // Réintégrer la quantité dans le lot
            $lot->quantite += $quantite;
            $lot->save();

            $produit = Produit::find($lot->produit_id);
            if ($produit) {
                $produit->updateQuantiteFromLots(); // Cette méthode doit sommer les quantités des lots
            } else {
                throw new \Exception("Produit avec l'ID {$lot->produit_id} introuvable lors de la réintégration du stock.");
            }

            // Mise à jour du stock global (Assurez-vous de la cohérence de cette logique de mise à jour)
            // Vous pouvez appeler ici la méthode de mise à jour du stock global (Produit->updateQuantiteFromLots() ou mettreAJourStock())
            // ... (Votre logique de mise à jour de $stock) ...

            // Créer un mouvement de type "entrée"
            MouvementStock::create([
                'produit_id'   => $lot->produit_id,
                'type'         => 'entree',
                'quantite'     => $quantite,
                'source_type'  => $sourceType, // Utilisation du sourceType fourni (ex: 'retour_client')
                'source_id'    => $sourceId,   // Utilisation du sourceId fourni (ex: ID du retour client)
                'lot_id'       => $lot->id,
                'magasin_id'   => $magasinId,
                'user_id'      => $userId,
                'motif'        => $motif,
                'date'         => now(),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
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
