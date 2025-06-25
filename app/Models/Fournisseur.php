<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fournisseur extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'email', 'telephone', 'adresse', 'magasin_id'];

    public function magasin()
    {
        return $this->belongsTo(Magasin::class);
    }
    
    public function commandes()
    {
        return $this->hasMany(Commande::class);
    }
}
