<?php

namespace App\Features\Audit\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class JournalAudit extends Model
{
    public $timestamps = false;

    protected $table = 'journal_audit';

    protected $fillable = [
        'user_id',
        'action',
        'type_objet',
        'objet_id',
        'avant',
        'apres',
        'adresse_ip',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'avant' => 'array',
            'apres' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public static function record(string $action, Model $model, ?array $before, ?array $after): void
    {
        self::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'type_objet' => $model::class,
            'objet_id' => $model->getKey(),
            'avant' => $before,
            'apres' => $after,
            'adresse_ip' => Request::ip(),
            'created_at' => now(),
        ]);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function objet(): MorphTo
    {
        return $this->morphTo('objet', 'type_objet', 'objet_id');
    }
}
