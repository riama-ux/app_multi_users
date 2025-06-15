<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perte extends Model
{
    use HasFactory;

    protected $fillable = ['produit_id', 'magasin_id', 'quantite', 'motif'];

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    public function magasin()
    {
        return $this->belongsTo(Magasin::class);
    }
}
