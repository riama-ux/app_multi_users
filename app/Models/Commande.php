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
        'date_prevue_livraison',
        'date_reception',
        'is_late',
        'days_late',
        'cout_transport',
        'frais_suppl',
        'cout_total'
    ];

   /* protected $casts = [
        'date_commande' => 'date',
    ];*/

    protected $casts = [
        'date_commande' => 'datetime',
        'date_prevue_livraison' => 'datetime',
        'date_reception' => 'datetime',
        'is_late' => 'boolean', 
        'days_late' => 'integer',
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
