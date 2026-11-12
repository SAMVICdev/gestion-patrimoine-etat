<?php

namespace App\Features\Biens\Http\Requests;

use App\Features\Biens\Models\Bien;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

abstract class BienRequest extends FormRequest
{
    protected function bienRules(): array
    {
        return [
            'numero_inventaire' => [
                'string',
                'max:100',
                Rule::unique('biens', 'numero_inventaire')->ignore($this->route('bien')),
            ],
            'designation' => ['string', 'max:255'],
            'description' => ['nullable', 'string'],
            'date_acquisition' => ['nullable', 'date'],
            'valeur_acquisition' => ['nullable', 'numeric', 'min:0', 'max:9999999999999.99'],
            'source_financement' => ['nullable', 'string', 'max:255'],
            'categorie_id' => ['integer', 'exists:categories,id'],
            'service_id' => ['integer', 'exists:services,id'],
            'localisation' => ['string', 'max:255'],
            'responsable' => ['nullable', 'string', 'max:255'],
            'etat' => ['string', Rule::in(Bien::ETATS)],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
