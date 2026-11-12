<?php

namespace App\Features\Transferts\Models;

use App\Features\Audit\Models\JournalAudit;
use App\Features\Biens\Models\Bien;
use App\Features\Services\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transfert extends Model
{
    public const STATUTS = ['en_attente', 'approuve', 'rejete'];

    protected $fillable = [
        'bien_id',
        'service_origine_id',
        'service_destination_id',
        'localisation_origine',
        'localisation_destination',
        'motif',
        'justificatif',
        'statut',
        'demande_par',
        'decision_par',
        'commentaire_decision',
        'date_demande',
        'date_decision',
        'date_effet',
    ];

    protected function casts(): array
    {
        return [
            'date_demande' => 'datetime',
            'date_decision' => 'datetime',
            'date_effet' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::created(fn (Transfert $transfert) => JournalAudit::record('demande_transfert', $transfert, null, $transfert->getAttributes()));
        static::updated(fn (Transfert $transfert) => JournalAudit::record('decision_transfert', $transfert, $transfert->getOriginal(), $transfert->getChanges()));
    }

    public function bien(): BelongsTo
    {
        return $this->belongsTo(Bien::class)->withTrashed();
    }

    public function serviceOrigine(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_origine_id');
    }

    public function serviceDestination(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_destination_id');
    }

    public function demandeur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'demande_par');
    }

    public function decideur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decision_par');
    }
}
