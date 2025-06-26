<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'magasin_id'];

    public function magasin()
    {
        return $this->belongsTo(Magasin::class);
    }

    public function produits()
    {
        return $this->hasMany(Produit::class);
    }
}
