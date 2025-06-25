<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Credit extends Model
{
    use HasFactory;

    protected $fillable = ['vente_id', 'client_id', 'magasin_id','montant', 'statut', 'echeance',];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    
    public function vente()
    {
        return $this->belongsTo(Vente::class);
    }

    public function magasin()
    {
        return $this->belongsTo(Magasin::class);
    }
}
