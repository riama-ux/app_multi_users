<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MouvementStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'produit_id',
        'magasin_id',
        'type', // entree ou sortie
        'quantite',
        'source_type',
        'source_id',
        'lot_id',
        'user_id',
        'motif',
        'date'
    ];

    protected $casts = [
        'date' => 'datetime',
    ];


    public function produit()
    {
        return $this->belongsTo(Produit::class)->withTrashed();
    }

    public function magasin()
    {
        return $this->belongsTo(Magasin::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lot()
    {
        return $this->belongsTo(StockLot::class, 'lot_id');
    }

    public function source()
    {
        return $this->morphTo();
    }


    /**
     * Accesseur pour savoir si le mouvement provient d'une commande livrée en retard.
     * Nécessite que la relation 'source' soit chargée (eager loaded).
     *
     * @return bool
     */
    public function getIsCommandeLateAttribute(): bool
    {
        // Vérifie si la source est une commande et si la commande est chargée
        if ($this->source_type === 'App\\Models\\Commande' && $this->source) {
            // Retourne la valeur de 'is_late' de la commande liée
            return (bool) $this->source->is_late;
        }
        return false; // Par défaut, pas en retard si ce n'est pas une commande ou si la source n'est pas chargée
    }

    /**
     * Accesseur pour obtenir le nombre de jours de retard si le mouvement
     * provient d'une commande livrée en retard.
     * Nécessite que la relation 'source' soit chargée (eager loaded).
     *
     * @return int|null
     */
    public function getDaysCommandeLateAttribute(): ?int
    {
        // Vérifie si la source est une commande et si la commande est chargée
        if ($this->source_type === 'App\\Models\\Commande' && $this->source) {
            // Retourne la valeur de 'days_late' de la commande liée
            return (int) $this->source->days_late;
        }
        return null; // Ou 0, selon votre préférence si ce n'est pas une commande ou pas en retard
    }

}
