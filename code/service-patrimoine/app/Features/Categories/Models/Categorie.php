<?php

namespace App\Features\Categories\Models;

use App\Features\Audit\Models\JournalAudit;
use App\Features\Biens\Models\Bien;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categorie extends Model
{
    protected static function booted(): void
    {
        static::created(fn (Categorie $categorie) => JournalAudit::record('creation_categorie', $categorie, null, $categorie->getAttributes()));
        static::updated(fn (Categorie $categorie) => JournalAudit::record('modification_categorie', $categorie, $categorie->getOriginal(), $categorie->getChanges()));
    }

    protected $fillable = [
        'nom',
        'description',
    ];

    public function biens(): HasMany
    {
        return $this->hasMany(Bien::class);
    }
}
