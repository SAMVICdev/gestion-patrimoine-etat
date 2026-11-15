<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Features\Audit\Models\JournalAudit;
use App\Features\Inventaires\Models\CampagneInventaire;
use App\Features\Services\Models\Service;
use App\Features\Transferts\Models\Transfert;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLES = [
        'administrateur_national',
        'gestionnaire',
        'agent',
    ];

    protected static function booted(): void
    {
        static::created(fn (User $user) => JournalAudit::record('creation_utilisateur', $user, null, $user->only(['name', 'email', 'role', 'service_id'])));
        static::updated(function (User $user): void {
            $changes = array_intersect_key($user->getChanges(), array_flip(['name', 'email', 'role', 'service_id']));

            if ($changes !== []) {
                JournalAudit::record('modification_utilisateur', $user, null, $changes);
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'service_id',
        'auth_user_id',
        'preferences',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'auth_user_id' => 'integer',
            'preferences' => 'array',
            'service_id' => 'integer',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function campagnesCreees(): HasMany
    {
        return $this->hasMany(CampagneInventaire::class, 'cree_par');
    }

    public function demandesTransfert(): HasMany
    {
        return $this->hasMany(Transfert::class, 'demande_par');
    }

    public function isNationalAdministrator(): bool
    {
        return $this->role === 'administrateur_national';
    }

    public function canManageAssets(): bool
    {
        return in_array($this->role, ['administrateur_national', 'gestionnaire'], true);
    }
}
