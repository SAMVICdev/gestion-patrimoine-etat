<?php

namespace App\Features\Ministeres\Models;

use App\Features\Audit\Models\JournalAudit;
use App\Features\Biens\Models\Bien;
use App\Features\Services\Models\Service;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Ministere extends Model
{
    protected static function booted(): void
    {
        static::created(fn (Ministere $ministere) => JournalAudit::record('creation_ministere', $ministere, null, $ministere->getAttributes()));
        static::updated(fn (Ministere $ministere) => JournalAudit::record('modification_ministere', $ministere, $ministere->getOriginal(), $ministere->getChanges()));
    }

    protected $fillable = [
        'nom',
        'code',
        'description',
    ];

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function biens(): HasManyThrough
    {
        return $this->hasManyThrough(Bien::class, Service::class, 'ministere_id', 'service_id');
    }
}
