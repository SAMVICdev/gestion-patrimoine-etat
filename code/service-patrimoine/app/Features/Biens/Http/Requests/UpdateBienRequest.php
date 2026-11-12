<?php

namespace App\Features\Biens\Http\Requests;

use App\Features\Biens\Models\Bien;
use Illuminate\Validation\Validator;

class UpdateBienRequest extends BienRequest
{
    public function rules(): array
    {
        return [
            ...array_map(
                static fn (array $rules): array => ['sometimes', ...$rules],
                $this->bienRules(),
            ),
            'commentaire_etat' => ['nullable', 'string', 'max:2000'],
            'justificatif_etat' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $bien = $this->route('bien');

            if ($bien instanceof Bien && $this->input('etat') !== null
                && $this->input('etat') !== $bien->etat
                && trim((string) $this->input('commentaire_etat')) === '') {
                $validator->errors()->add('commentaire_etat', 'Un commentaire est requis lors d’un changement d’état.');
            }

            if ($bien instanceof Bien && $this->hasFile('justificatif_etat')
                && ($this->input('etat') === null || $this->input('etat') === $bien->etat)) {
                $validator->errors()->add('justificatif_etat', 'Un justificatif doit accompagner un changement d’état.');
            }
        });
    }
}
