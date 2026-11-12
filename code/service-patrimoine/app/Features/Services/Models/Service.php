<?php

namespace App\Features\Services\Models;

use App\Features\Biens\Models\Bien;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
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
