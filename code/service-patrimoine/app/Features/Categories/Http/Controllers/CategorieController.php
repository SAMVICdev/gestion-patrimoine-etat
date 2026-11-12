<?php

namespace App\Features\Categories\Http\Controllers;

use App\Features\Categories\Http\Requests\StoreCategorieRequest;
use App\Features\Categories\Http\Requests\UpdateCategorieRequest;
use App\Features\Categories\Http\Resources\CategorieResource;
use App\Features\Categories\Models\Categorie;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategorieController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        return CategorieResource::collection(
            Categorie::query()->orderBy('nom')->paginate($validated['per_page'] ?? 15),
        );
    }

    public function store(StoreCategorieRequest $request): JsonResponse
    {
        $categorie = Categorie::create($request->validated());

        return (new CategorieResource($categorie))->response()->setStatusCode(201);
    }

    public function show(Categorie $categorie): CategorieResource
    {
        return new CategorieResource($categorie);
    }

    public function update(UpdateCategorieRequest $request, Categorie $categorie): CategorieResource
    {
        $categorie->update($request->validated());

        return new CategorieResource($categorie);
    }
}
