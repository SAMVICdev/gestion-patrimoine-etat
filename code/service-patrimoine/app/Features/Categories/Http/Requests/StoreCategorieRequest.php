<?php

namespace App\Features\Categories\Http\Requests;

class StoreCategorieRequest extends CategorieRequest
{
    public function rules(): array
    {
        return [
            'nom' => ['required', ...$this->categorieRules()['nom']],
            'description' => ['sometimes', ...$this->categorieRules()['description']],
        ];
    }
}
