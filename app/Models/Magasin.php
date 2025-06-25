<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Magasin extends Model
{
    use HasFactory;
    protected $fillable = ['nom', 'adresse'];

    public function categories()
    {
        return $this->hasMany(Categorie::class);
    }


    // Relation many-to-many avec les utilisateurs
    public function users()
    {
        return $this->belongsToMany(User::class, 'magasin_user')->withTimestamps();
    }

    // Relation one-to-many avec les stocks
    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    // Relation one-to-many avec les produits si applicable
    public function produits()
    {
        return $this->hasMany(Produit::class);
    }

    // Relation avec commandes si un magasin les possède directement
    public function commandes()
    {
        return $this->hasMany(Commande::class);
    }

    // Relation avec transferts en tant que source
    public function transfertsSource()
    {
        return $this->hasMany(Transfert::class, 'magasin_source_id');
    }

    // Relation avec transferts en tant que destination
    public function transfertsDestination()
    {
        return $this->hasMany(Transfert::class, 'magasin_destination_id');
    }
    
}
