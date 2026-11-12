<?php

namespace App\Features\Biens\Http\Requests;

use App\Features\Biens\Models\Bien;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexBiensRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'categorie_id' => ['sometimes', 'integer', 'exists:categories,id'],
            'service_id' => ['sometimes', 'integer', 'exists:services,id'],
            'etat' => ['sometimes', 'string', Rule::in(Bien::ETATS)],
            'q' => ['sometimes', 'string', 'max:100'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
