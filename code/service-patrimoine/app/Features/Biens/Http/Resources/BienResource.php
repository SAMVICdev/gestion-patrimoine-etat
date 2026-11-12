<?php

namespace App\Features\Biens\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BienResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'numero_inventaire' => $this->numero_inventaire,
            'designation' => $this->designation,
            'description' => $this->description,
            'date_acquisition' => $this->date_acquisition?->toDateString(),
            'valeur_acquisition' => $this->valeur_acquisition,
            'source_financement' => $this->source_financement,
            'categorie' => $this->whenLoaded('categorie', fn () => [
                'id' => $this->categorie->id,
                'nom' => $this->categorie->nom,
            ]),
            'service' => $this->whenLoaded('service', fn () => [
                'id' => $this->service->id,
                'nom' => $this->service->nom,
            ]),
            'localisation' => $this->localisation,
            'responsable' => $this->responsable,
            'etat' => $this->etat,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
