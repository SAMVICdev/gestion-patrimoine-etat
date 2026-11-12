<?php

namespace App\Features\Biens\Http\Controllers;

use App\Features\Biens\Http\Requests\IndexBiensRequest;
use App\Features\Biens\Http\Requests\StoreBienRequest;
use App\Features\Biens\Http\Requests\UpdateBienRequest;
use App\Features\Biens\Http\Resources\BienResource;
use App\Features\Biens\Models\Bien;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class BienController extends Controller
{
    public function index(IndexBiensRequest $request)
    {
        $filters = $request->validated();

        $biens = Bien::query()
            ->with(['categorie', 'service'])
            ->when(isset($filters['categorie_id']), fn (Builder $query) => $query->where('categorie_id', $filters['categorie_id']))
            ->when(isset($filters['service_id']), fn (Builder $query) => $query->where('service_id', $filters['service_id']))
            ->when(isset($filters['etat']), fn (Builder $query) => $query->where('etat', $filters['etat']))
            ->when(isset($filters['q']), function (Builder $query) use ($filters): void {
                $search = $filters['q'];
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('numero_inventaire', 'like', "%{$search}%")
                        ->orWhere('designation', 'like', "%{$search}%")
                        ->orWhere('localisation', 'like', "%{$search}%");
                });
            })
            ->orderBy('id')
            ->paginate($filters['per_page'] ?? 15);

        return BienResource::collection($biens);
    }

    public function store(StoreBienRequest $request): JsonResponse
    {
        $bien = Bien::create($request->validated())->load(['categorie', 'service']);

        return (new BienResource($bien))->response()->setStatusCode(201);
    }

    public function show(Bien $bien): BienResource
    {
        return new BienResource($bien->load(['categorie', 'service']));
    }

    public function update(UpdateBienRequest $request, Bien $bien): BienResource
    {
        $bien->update($request->validated());

        return new BienResource($bien->load(['categorie', 'service']));
    }

    public function destroy(Bien $bien): Response
    {
        $bien->delete();

        return response()->noContent();
    }
}
