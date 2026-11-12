<?php

namespace App\Features\Categories\Http\Requests;

class UpdateCategorieRequest extends CategorieRequest
{
    public function rules(): array
    {
        return [
            'nom' => ['sometimes', 'required', ...$this->categorieRules()['nom']],
            'description' => ['sometimes', ...$this->categorieRules()['description']],
        ];
    }
}
