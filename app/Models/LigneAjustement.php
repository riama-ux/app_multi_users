<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LigneAjustement extends Model
{
    use HasFactory;

    protected $table = 'ligne_ajustements'; // Assurez-vous que le nom de la table est correct

    protected $fillable = [
        'ajustement_id',
        'produit_id',
        'quantite_ajustee',
        'prix_unitaire_ajuste',
        'motif_ligne',
    ];

    protected $casts = [
        'quantite_ajustee' => 'decimal:2',
        'prix_unitaire_ajuste' => 'decimal:2',
    ];

    /**
     * Get the adjustment that owns the line adjustment.
     */
    public function ajustement()
    {
        return $this->belongsTo(Ajustement::class);
    }

    /**
     * Get the product associated with the line adjustment.
     */
    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }
}
