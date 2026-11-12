<?php

namespace App\Features\Biens\Http\Requests;

class StoreBienRequest extends BienRequest
{
    public function rules(): array
    {
        $requiredFields = [
            'numero_inventaire',
            'designation',
            'categorie_id',
            'service_id',
            'localisation',
        ];

        return collect($this->bienRules())
            ->map(fn (array $rules, string $field): array => in_array($field, $requiredFields, true)
                ? ['required', ...$rules]
                : ['sometimes', ...$rules])
            ->all();
    }
}
