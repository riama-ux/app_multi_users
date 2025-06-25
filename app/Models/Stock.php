<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = ['produit_id', 'magasin_id', 'quantite'];

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    public function magasin()
    {
        return $this->belongsTo(Magasin::class);
    }

    public function stock()
    {
        return $this->hasOne(Stock::class)->where('magasin_id', session('magasin_actif_id'));
    }
}
