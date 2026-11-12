<?php

namespace App\Features\Biens\Models;

use App\Features\Categories\Models\Categorie;
use App\Features\Services\Models\Service;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bien extends Model
{
    use SoftDeletes;

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
}
