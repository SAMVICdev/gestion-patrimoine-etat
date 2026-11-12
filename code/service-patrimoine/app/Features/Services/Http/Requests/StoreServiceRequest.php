<?php

namespace App\Features\Services\Http\Requests;

class StoreServiceRequest extends ServiceRequest
{
    public function rules(): array
    {
        return [
            'nom' => ['required', ...$this->serviceRules()['nom']],
            'description' => ['sometimes', ...$this->serviceRules()['description']],
        ];
    }
}
