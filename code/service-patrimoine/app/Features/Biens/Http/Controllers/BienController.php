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
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class BienController extends Controller
{
    public function index(IndexBiensRequest $request)
    {
        $filters = $request->validated();

        $biens = Bien::query()
            ->with(['categorie', 'service'])
            ->when(
                ! $request->user()->isNationalAdministrator(),
                fn (Builder $query) => $request->user()->service_id
                    ? $query->where('service_id', $request->user()->service_id)
                    : $query->whereRaw('1 = 0'),
            )
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
        $data = $request->validated();
        abort_if(! $request->user()->isNationalAdministrator() && $request->user()->service_id !== $data['service_id'], 403);
        $bien = Bien::create($data)->load(['categorie', 'service']);

        return (new BienResource($bien))->response()->setStatusCode(201);
    }

    public function show(Request $request, Bien $bien): BienResource
    {
        $this->authorizeBien($request, $bien);

        return new BienResource($bien->load(['categorie', 'service']));
    }

    public function update(UpdateBienRequest $request, Bien $bien): BienResource
    {
        $this->authorizeBien($request, $bien);
        $data = $request->validated();
        $comment = $data['commentaire_etat'] ?? null;
        unset($data['commentaire_etat'], $data['justificatif_etat']);
        $justificatif = null;

        try {
            DB::transaction(function () use ($request, $bien, $data, $comment, &$justificatif): void {
                $bien = Bien::query()->lockForUpdate()->findOrFail($bien->id);
                $this->authorizeBien($request, $bien);
                abort_if(
                    isset($data['service_id'])
                        && ! $request->user()->isNationalAdministrator()
                        && $request->user()->service_id !== $data['service_id'],
                    403,
                );

                $stateChanged = isset($data['etat']) && $data['etat'] !== $bien->etat;
                $bien->update($data);

                if ($stateChanged) {
                    $justificatif = $request->file('justificatif_etat')?->store('justificatifs/etats', 'local');
                    if ($request->hasFile('justificatif_etat') && ! $justificatif) {
                        throw new \RuntimeException('Impossible d’enregistrer le justificatif du changement d’état.');
                    }

                    $bien->historiqueEtats()->latest('id')->firstOrFail()->update([
                        'commentaire' => $comment,
                        'justificatif' => $justificatif,
                    ]);
                }
            });
        } catch (Throwable $exception) {
            if ($justificatif) {
                Storage::disk('local')->delete($justificatif);
            }

            throw $exception;
        }

        return new BienResource($bien->fresh()->load(['categorie', 'service']));
    }

    public function destroy(Request $request, Bien $bien): Response
    {
        $this->authorizeBien($request, $bien);
        $bien->delete();

        return response()->noContent();
    }

    private function authorizeBien(Request $request, Bien $bien): void
    {
        abort_if(
            ! $request->user()->isNationalAdministrator()
                && $request->user()->service_id !== $bien->service_id,
            404,
        );
    }
}
