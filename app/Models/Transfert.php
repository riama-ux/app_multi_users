<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfert extends Model
{
    use HasFactory;

    protected $fillable = [
        'magasin_source_id',
        'magasin_dest_id',
        'user_id',
        'date_transfert'
    ];

    public function magasinSource()
    {
        return $this->belongsTo(Magasin::class, 'magasin_source_id');
    }

    public function magasinDest()
    {
        return $this->belongsTo(Magasin::class, 'magasin_dest_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ligneTransferts()
    {
        return $this->hasMany(LigneTransfert::class);
    }
}
