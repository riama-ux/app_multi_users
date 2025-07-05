<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    use HasFactory;

    protected $fillable = [
        'fournisseur_id', 'user_id', 'magasin_id',
        'statut',
        'date_commande',
        'date_reception',
        'cout_transport',
        'frais_suppl',
        'cout_total'
    ];

    protected $casts = [
        'date_commande' => 'date',
    ];


    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function magasin()
    {
        return $this->belongsTo(Magasin::class);
    }

    public function lignesCommande()
    {
        return $this->hasMany(LigneCommande::class);
    }
}
