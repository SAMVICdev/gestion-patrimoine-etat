<?php

namespace App\Features\Biens\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BienPhoto extends Model
{
    protected $fillable = [
        'bien_id',
        'path',
        'nom_original',
        'legende',
        'ajoute_par',
    ];

    public function bien(): BelongsTo
    {
        return $this->belongsTo(Bien::class);
    }

    public function auteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ajoute_par');
    }
}
