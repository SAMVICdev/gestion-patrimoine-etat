<?php

namespace App\Features\Categories\Models;

use App\Features\Biens\Models\Bien;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categorie extends Model
{
    protected $fillable = [
        'nom',
        'description',
    ];

    public function biens(): HasMany
    {
        return $this->hasMany(Bien::class);
    }
}
