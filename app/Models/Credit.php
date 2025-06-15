<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Credit extends Model
{
    use HasFactory;

    protected $fillable = ['vente_id', 'montant_restant', 'date_echeance'];

    public function vente()
    {
        return $this->belongsTo(Vente::class);
    }
}
