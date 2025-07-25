<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfert extends Model
{
    use HasFactory;

    protected $fillable = [
        'magasin_source_id',
        'magasin_destination_id',
        'user_id',
        'date_transfert',
        'statut',
    ];

    protected $casts = [
        'date_transfert' => 'datetime',
    ];


    public function magasinSource()
    {
        return $this->belongsTo(Magasin::class, 'magasin_source_id');
    }

    public function magasinDestination()
    {
        return $this->belongsTo(Magasin::class, 'magasin_destination_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ligneTransferts()
    {
        return $this->hasMany(LigneTransfert::class);
    }

    public function mouvements()
    {
        return $this->morphMany(MouvementStock::class, 'source');
    }

}
