<?php

namespace App\Features\Categories\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

abstract class CategorieRequest extends FormRequest
{
    protected function categorieRules(): array
    {
        return [
            'nom' => [
                'string',
                'max:255',
                Rule::unique('categories', 'nom')->ignore($this->route('categorie')),
            ],
            'description' => ['nullable', 'string'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
