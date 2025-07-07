<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use HasFactory;

    protected $fillable = [
        'vente_id',
        'montant',
        'mode_paiement',
        'date_paiement',
        'user_id',
        'annule',
    ];

    protected $casts = [
        'date_paiement' => 'datetime',
    ];


    public function vente()
    {
        return $this->belongsTo(Vente::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
