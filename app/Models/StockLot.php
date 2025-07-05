<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockLot extends Model
{
    use HasFactory;

    protected $fillable = [
        'produit_id',
        'magasin_id',
        'quantite',
        'quantite_restante',
        'cout_achat',
        'date_reception',
    ];

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    public function magasin()
    {
        return $this->belongsTo(Magasin::class);
    }
}
