<?php

namespace App\Features\Services\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

abstract class ServiceRequest extends FormRequest
{
    protected function serviceRules(): array
    {
        return [
            'nom' => [
                'string',
                'max:255',
                Rule::unique('services', 'nom')->ignore($this->route('service')),
            ],
            'description' => ['nullable', 'string'],
            'ministere_id' => ['nullable', 'integer', 'exists:ministeres,id'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
