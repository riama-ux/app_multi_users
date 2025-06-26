<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom', 'categorie_id', 'magasin_id', 'code', 
        'prix_achat', 'cout_achat', 'prix_vente', 'description'
    ];

    protected static function booted()
    {
        static::creating(function ($produit) {
            if (empty($produit->code)) {
                $produit->code = strtoupper(uniqid('PRD'));
            }
        });
    }

    public function magasin()
    {
        return $this->belongsTo(Magasin::class);
    }


    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function ventes()
    {
        return $this->hasMany(Vente::class);
    }

    public function pertes()
    {
        return $this->hasMany(Perte::class);
    }

    public function transferts()
    {
        return $this->hasMany(Transfert::class);
    }

    public function commandes()
    {
        return $this->hasMany(Commande::class);
    }
}
