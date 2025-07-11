<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LigneRetourClient extends Model
{
    use HasFactory;

    protected $fillable = [
        'retour_client_id',
        'produit_id',
        'quantite_retournee',
        'prix_unitaire_retour', // Prix auquel le produit a été vendu/remboursé
        'motif_ligne',
        'lot_id', // Le lot d'origine si connu, pour réintégrer au bon lot si possible (ou créer un nouveau)
    ];

    protected $casts = [
        'quantite_retournee' => 'decimal:2',
        'prix_unitaire_retour' => 'decimal:2',
    ];

    public function retourClient()
    {
        return $this->belongsTo(RetourClient::class);
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    public function lot()
    {
        return $this->belongsTo(StockLot::class, 'lot_id');
    }
}
