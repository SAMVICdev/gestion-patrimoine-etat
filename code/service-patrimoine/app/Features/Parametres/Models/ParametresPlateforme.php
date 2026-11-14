<?php

namespace App\Features\Parametres\Models;

use Illuminate\Database\Eloquent\Model;

class ParametresPlateforme extends Model
{
    protected $table = 'parametres_plateforme';

    protected $fillable = ['cle', 'valeur', 'description'];

    /**
     * Récupère la valeur d'un paramètre par sa clé.
     */
    public static function get(string $cle, string $default = ''): string
    {
        $param = static::query()->where('cle', $cle)->first();

        return $param?->valeur ?? $default;
    }

    /**
     * Définit ou met à jour la valeur d'un paramètre.
     */
    public static function set(string $cle, string $valeur): void
    {
        static::query()->where('cle', $cle)->update(['valeur' => $valeur]);
    }

    /**
     * Retourne tous les paramètres sous forme de tableau clé => valeur.
     *
     * @return array<string, string>
     */
    public static function allAsArray(): array
    {
        return static::query()->pluck('valeur', 'cle')->all();
    }
}
