<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    use HasFactory;

    protected $fillable = [
        'fournisseur_id', 'produit_id', 'quantite', 'prix_total', 'statut'
    ];

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }
}
