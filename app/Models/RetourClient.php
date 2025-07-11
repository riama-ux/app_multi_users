<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetourClient extends Model
{
    use HasFactory;

    protected $fillable = [
        'vente_id',
        'client_id',
        'magasin_id',
        'user_id',
        'date_retour',
        'montant_rembourse', // Si un remboursement est effectué
        'motif_global',
        'statut', // Ex: 'en_attente', 'traite', 'rembourse'
    ];

    protected $casts = [
        'date_retour' => 'datetime',
        'montant_rembourse' => 'decimal:0',
    ];

    public function vente()
    {
        return $this->belongsTo(Vente::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function magasin()
    {
        return $this->belongsTo(Magasin::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lignesRetour()
    {
        return $this->hasMany(LigneRetourClient::class);
    }
}
