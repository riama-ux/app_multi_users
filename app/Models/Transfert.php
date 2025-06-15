<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfert extends Model
{
    use HasFactory;

    protected $fillable = [
        'produit_id', 'magasin_source_id', 'magasin_destination_id', 'quantite', 'etat'
    ];

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    public function magasinSource()
    {
        return $this->belongsTo(Magasin::class, 'magasin_source_id');
    }

    public function magasinDestination()
    {
        return $this->belongsTo(Magasin::class, 'magasin_destination_id');
    }
}
