<?php

namespace App\Features\Inventaires\Models;

use App\Features\Audit\Models\JournalAudit;
use App\Features\Biens\Models\Bien;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConstatInventaire extends Model
{
    public const RESULTATS = ['present', 'introuvable', 'degrade'];

    protected $table = 'constats_inventaire';

    protected $fillable = [
        'campagne_id',
        'bien_id',
        'resultat',
        'commentaire',
        'agent_id',
        'date_constat',
    ];

    protected function casts(): array
    {
        return ['date_constat' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::created(fn (ConstatInventaire $constat) => JournalAudit::record('constat_inventaire', $constat, null, $constat->getAttributes()));
    }

    public function campagne(): BelongsTo
    {
        return $this->belongsTo(CampagneInventaire::class, 'campagne_id');
    }

    public function bien(): BelongsTo
    {
        return $this->belongsTo(Bien::class)->withTrashed();
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }
}
