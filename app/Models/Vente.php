<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vente extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'client_id',
        'magasin_id',
        //'total_ht',
        'remise',
        'total_ttc',
        'montant_paye',
        'reste_a_payer',
        'statut',
        'mode_paiement',
        'date_vente'
    ];

    protected $dates = ['date_vente'];

    protected $casts = [
        'date_vente' => 'datetime',
    ];


    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function magasin()
    {
        return $this->belongsTo(Magasin::class);
    }

    public function credit()
    {
        return $this->hasOne(Credit::class);
    }

    public function user()
    {
        // Suppose que la colonne qui stocke l'id de l'utilisateur est 'user_id'
        return $this->belongsTo(User::class);
    }

    public function ligneVentes()
    {
        return $this->hasMany(LigneVente::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }
}
