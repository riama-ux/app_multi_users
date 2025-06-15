<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vente extends Model
{
    use HasFactory;

    protected $fillable = [
        'produit_id', 'magasin_id', 'client_id',
        'quantite', 'prix_unitaire', 'total', 'mode_paiement'
    ];

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

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
}
