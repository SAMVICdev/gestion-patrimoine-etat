<?php

namespace App\Features\Transferts\Models;

use App\Features\Audit\Models\JournalAudit;
use App\Features\Biens\Models\Bien;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoriqueEtat extends Model
{
    protected $table = 'historique_etats';

    protected static function booted(): void
    {
        static::updated(fn (HistoriqueEtat $historique) => JournalAudit::record(
            'mise_a_jour_historique_etat',
            $historique,
            null,
            $historique->getChanges(),
        ));
    }

    protected $fillable = [
        'bien_id',
        'etat_origine',
        'etat_destination',
        'commentaire',
        'justificatif',
        'modifie_par',
        'date_modification',
    ];

    protected function casts(): array
    {
        return ['date_modification' => 'datetime'];
    }

    public function bien(): BelongsTo
    {
        return $this->belongsTo(Bien::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'modifie_par');
    }
}
