<?php

namespace App\Features\Services\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'nom',
        'description',
    ];
}
