<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MouvementStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'produit_id',
        'magasin_id',
        'type', // entree ou sortie
        'quantite',
        'source_type',
        'source_id',
        'lot_id',
        'user_id',
        'motif',
        'date'
    ];

    protected $casts = [
        'date' => 'datetime',
    ];


    public function produit()
    {
        return $this->belongsTo(Produit::class)->withTrashed();
    }

    public function magasin()
    {
        return $this->belongsTo(Magasin::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lot()
    {
        return $this->belongsTo(StockLot::class, 'lot_id');
    }

    public function source()
    {
        return $this->morphTo();
    }

}
