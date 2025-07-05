<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LigneVente extends Model
{
    use HasFactory;

    protected $fillable = [
        'vente_id', 'produit_id', 'quantite', 'prix_unitaire', 'prix_total', 'lot_id',
    ];

    public function vente()
    {
        return $this->belongsTo(Vente::class);
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class)->withTrashed();
    }

    public function lot()
    {
        return $this->belongsTo(StockLot::class, 'lot_id');
    }
}
