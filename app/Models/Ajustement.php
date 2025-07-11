<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ajustement extends Model
{
    use HasFactory;

    protected $fillable = [
        'magasin_id',
        'type',
        'motif_global',
        'user_id',
        'date_ajustement',
    ];

    protected $casts = [
        'date_ajustement' => 'datetime',
    ];

    /**
     * Get the user that performed the adjustment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the store where the adjustment occurred.
     */
    public function magasin()
    {
        return $this->belongsTo(Magasin::class);
    }

    /**
     * Get the adjustment lines for the adjustment.
     */
    public function lignesAjustement()
    {
        return $this->hasMany(LigneAjustement::class);
    }
}
