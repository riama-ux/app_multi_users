<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LigneTransfert extends Model
{
    use HasFactory;

    protected $fillable = [
        'transfert_id',
        'produit_id',
        'quantite',
    ];

    public function transfert()
    {
        return $this->belongsTo(Transfert::class);
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class)->withTrashed();
    }
}
