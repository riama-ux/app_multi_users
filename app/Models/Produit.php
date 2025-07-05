<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nom', 'reference',
        'categorie_id',
        'magasin_id',
        'cout_achat',
        'prix_vente',
        'marge',
        'seuil_alerte'
    ];

    // Générer le code si vide
    protected static function booted()
    {
        static::creating(function ($produit) {
            if (empty($produit->reference)) {
                $produit->reference = strtoupper(uniqid('PRD'));
            }
        });

        // Supprimer les stocks liés lors d’un soft delete
        static::deleting(function ($produit) {
            foreach ($produit->stocks()->get() as $stock) {
                $stock->delete(); // soft delete du stock
            }
        });

        // Restaurer les stocks liés lors d’une restauration
        static::restoring(function ($produit) {
            foreach ($produit->stocks()->withTrashed()->get() as $stock) {
                $stock->restore();
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
        return $this->hasOne(Stock::class);
    }

    public function lignesCommande()
    {
        return $this->hasMany(LigneCommande::class);
    }

    public function lignesVente()
    {
        return $this->hasMany(LigneVente::class);
    }

    public function mouvementsStock()
    {
        return $this->hasMany(MouvementStock::class);
    }

    public function pertes()
    {
        return $this->hasMany(Perte::class);
    }

    public function transferts()
    {
        return $this->hasMany(Transfert::class);
    }

}
