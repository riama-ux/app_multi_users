<?php

namespace App\Services;

use App\Models\StockLot;
use App\Models\MouvementStock;
use Illuminate\Support\Facades\DB;
use App\Models\Stock;


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
        $stock = Stock::withTrashed()->firstOrCreate(
            [
                'produit_id' => $produitId,
                'magasin_id' => $magasinId,
            ],
            ['quantite' => 0]
        );

        if ($stock->trashed()) {
            $stock->restore();
        }

        $stock->update([
            'quantite' => StockLot::where('produit_id', $produitId)
                ->where('magasin_id', $magasinId)
                ->sum('quantite')
        ]);

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
            ->sum('quantite_restante');

        $stock = \App\Models\Stock::firstOrCreate([
            'produit_id' => $produit_id,
            'magasin_id' => $magasin_id,
        ]);

        $stock->update(['quantite' => $quantite]);
    }

    public static function reintegrerStockLot($lotId, $quantite, $magasinId, $userId = null, $motif = null)
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

            // Mise à jour du stock global (avec gestion soft delete comme dans sortirFifo)
            $stock = \App\Models\Stock::withTrashed()->firstOrCreate(
                [
                    'produit_id' => $lot->produit_id,
                    'magasin_id' => $magasinId,
                ],
                ['quantite' => 0]
            );

            if ($stock->trashed()) {
                $stock->restore();
            }

            $stock->update([
                'quantite' => StockLot::where('produit_id', $lot->produit_id)
                    ->where('magasin_id', $magasinId)
                    ->sum('quantite')
            ]);

            // Créer un mouvement de type "entrée"
            MouvementStock::create([
                'produit_id'   => $lot->produit_id,
                'type'         => 'entree',
                'quantite'     => $quantite,
                'source_type'  => 'correction', // ou 'modification_vente'
                'source_id'    => null,
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


}
