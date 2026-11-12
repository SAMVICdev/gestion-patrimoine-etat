<?php

namespace App\Features\Services\Models;

use App\Features\Audit\Models\JournalAudit;
use App\Features\Biens\Models\Bien;
use App\Features\Ministeres\Models\Ministere;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected static function booted(): void
    {
        static::created(fn (Service $service) => JournalAudit::record('creation_service', $service, null, $service->getAttributes()));
        static::updated(fn (Service $service) => JournalAudit::record('modification_service', $service, $service->getOriginal(), $service->getChanges()));
    }

    protected $fillable = [
        'nom',
        'description',
        'ministere_id',
    ];

    public function biens(): HasMany
    {
        return $this->hasMany(Bien::class);
    }

    public function ministere(): BelongsTo
    {
        return $this->belongsTo(Ministere::class);
    }
}
