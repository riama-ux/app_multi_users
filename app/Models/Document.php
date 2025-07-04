<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'numero',
        'mention',
        'nature',
        'date_signature',
        'notification',
        'dossier',
        'reference',
        'fichier',
        'etat',
        'details',
    ];
}
