<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['produit_id', 'magasin_id', 'quantite'];

    public function produit()
    {
        return $this->belongsTo(Produit::class)->withTrashed();
    }

    public function magasin()
    {
        return $this->belongsTo(Magasin::class);
    }

    
}
