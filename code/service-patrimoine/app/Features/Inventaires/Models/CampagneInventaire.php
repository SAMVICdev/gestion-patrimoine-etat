<?php

namespace App\Features\Inventaires\Models;

use App\Features\Audit\Models\JournalAudit;
use App\Features\Services\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CampagneInventaire extends Model
{
    protected $table = 'campagnes_inventaire';

    protected $fillable = [
        'titre',
        'description',
        'statut',
        'service_id',
        'cree_par',
        'cloture_par',
        'date_debut',
        'date_cloture',
    ];

    protected function casts(): array
    {
        return [
            'date_debut' => 'datetime',
            'date_cloture' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::created(fn (CampagneInventaire $campagne) => JournalAudit::record('creation_inventaire', $campagne, null, $campagne->getAttributes()));
        static::updated(fn (CampagneInventaire $campagne) => JournalAudit::record('mise_a_jour_inventaire', $campagne, $campagne->getOriginal(), $campagne->getChanges()));
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cree_par');
    }

    public function constats(): HasMany
    {
        return $this->hasMany(ConstatInventaire::class, 'campagne_id')->latest('date_constat');
    }
}
