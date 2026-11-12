<?php

namespace App\Features\Biens\Http\Requests;

class UpdateBienRequest extends BienRequest
{
    public function rules(): array
    {
        return array_map(
            static fn (array $rules): array => ['sometimes', ...$rules],
            $this->bienRules(),
        );
    }
}
