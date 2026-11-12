<?php

namespace App\Features\Services\Http\Requests;

class UpdateServiceRequest extends ServiceRequest
{
    public function rules(): array
    {
        return [
            'nom' => ['sometimes', 'required', ...$this->serviceRules()['nom']],
            'description' => ['sometimes', ...$this->serviceRules()['description']],
        ];
    }
}
