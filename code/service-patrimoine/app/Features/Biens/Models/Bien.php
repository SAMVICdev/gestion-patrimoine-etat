<?php

namespace App\Features\Biens\Models;

use App\Features\Audit\Models\JournalAudit;
use App\Features\Categories\Models\Categorie;
use App\Features\Inventaires\Models\ConstatInventaire;
use App\Features\Services\Models\Service;
use App\Features\Transferts\Models\HistoriqueEtat;
use App\Features\Transferts\Models\Transfert;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Bien extends Model
{
    use SoftDeletes;

    protected static function booted(): void
    {
        static::created(function (Bien $bien): void {
            JournalAudit::record('creation', $bien, null, $bien->getAttributes());
        });

        static::updating(function (Bien $bien): void {
            $before = $bien->getOriginal();

            JournalAudit::record('modification', $bien, $before, $bien->getDirty());

            if ($bien->isDirty('etat')) {
                HistoriqueEtat::create([
                    'bien_id' => $bien->id,
                    'etat_origine' => $before['etat'],
                    'etat_destination' => $bien->etat,
                    'modifie_par' => Auth::id(),
                    'date_modification' => now(),
                ]);
            }
        });

        static::deleted(function (Bien $bien): void {
            JournalAudit::record('archivage', $bien, $bien->getOriginal(), [
                'deleted_at' => $bien->deleted_at,
            ]);
        });
    }

    public const ETATS = [
        'bon_etat',
        'etat_moyen',
        'a_reparer',
        'en_reparation',
        'hors_service',
        'introuvable',
    ];

    protected $fillable = [
        'numero_inventaire',
        'designation',
        'description',
        'date_acquisition',
        'valeur_acquisition',
        'source_financement',
        'categorie_id',
        'service_id',
        'localisation',
        'responsable',
        'etat',
    ];

    protected function casts(): array
    {
        return [
            'date_acquisition' => 'date',
            'valeur_acquisition' => 'decimal:2',
        ];
    }

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function transferts(): HasMany
    {
        return $this->hasMany(Transfert::class);
    }

    public function historiqueEtats(): HasMany
    {
        return $this->hasMany(HistoriqueEtat::class)->latest('date_modification');
    }

    public function constatsInventaire(): HasMany
    {
        return $this->hasMany(ConstatInventaire::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(BienPhoto::class)->latest();
    }

    /**
     * Valeur résiduelle estimée par amortissement linéaire.
     * Retourne null si la catégorie n'a pas de durée de vie définie.
     */
    public function valeurResiduelle(): ?float
    {
        $dureeVie = $this->categorie?->duree_vie_annees;
        if (! $dureeVie || $this->valeur_acquisition === null) {
            return null;
        }

        $debut = ($this->date_acquisition ?? $this->created_at)?->copy()->startOfDay();
        if (! $debut) {
            return null;
        }

        $ageAnnees = max(0, $debut->diffInYears(now()->startOfDay(), false));
        $taux = max(0.0, min(1.0, 1 - ($ageAnnees / $dureeVie)));

        return round((float) $this->valeur_acquisition * $taux, 2);
    }
}
