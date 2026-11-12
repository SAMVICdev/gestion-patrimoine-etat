<?php

namespace App\Features\Biens\Http\Controllers;

use App\Features\Biens\Models\Bien;
use App\Features\Categories\Models\Categorie;
use App\Features\Services\Models\Service;
use App\Features\Transferts\Models\HistoriqueEtat;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class BienWebController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['sometimes', 'string', 'max:100'],
            'categorie_id' => ['sometimes', 'integer', 'exists:categories,id'],
            'service_id' => ['sometimes', 'integer', 'exists:services,id'],
            'etat' => ['sometimes', 'string', Rule::in(Bien::ETATS)],
        ]);

        $biens = Bien::query()
            ->with(['categorie', 'service'])
            ->when(isset($filters['q']), function ($query) use ($filters): void {
                $search = $filters['q'];
                $query->where(function ($query) use ($search): void {
                    $query->where('numero_inventaire', 'like', "%{$search}%")
                        ->orWhere('designation', 'like', "%{$search}%")
                        ->orWhere('localisation', 'like', "%{$search}%");
                });
            })
            ->when(isset($filters['categorie_id']), fn ($query) => $query->where('categorie_id', $filters['categorie_id']))
            ->when(isset($filters['service_id']), fn ($query) => $query->where('service_id', $filters['service_id']))
            ->when(isset($filters['etat']), fn ($query) => $query->where('etat', $filters['etat']))
            ->when(
                ! $request->user()->isNationalAdministrator(),
                fn ($query) => $request->user()->service_id
                    ? $query->where('service_id', $request->user()->service_id)
                    : $query->whereRaw('1 = 0'),
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('biens.index', [
            'biens' => $biens,
            'categories' => Categorie::query()->orderBy('nom')->get(),
            'services' => $this->servicesFor(auth()->user()),
            'etats' => Bien::ETATS,
        ]);
    }

    public function create(): View
    {
        return view('biens.create', $this->formData(auth()->user()));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules());
        $this->authorizeService($request, (int) $data['service_id']);
        $bien = Bien::create($data);

        return redirect()->route('biens.show', $bien)->with('success', 'Le bien a été enregistré.');
    }

    public function show(Bien $bien): View
    {
        $this->authorizeBien(auth()->user(), $bien);

        return view('biens.show', [
            'bien' => $bien->load(['categorie', 'service']),
            'etatHistories' => $bien->historiqueEtats()->with('agent')->get(),
            'transferts' => $bien->transferts()->with(['serviceOrigine', 'serviceDestination', 'demandeur', 'decideur'])->latest()->get(),
        ]);
    }

    public function edit(Bien $bien): View
    {
        $this->authorizeBien(auth()->user(), $bien);

        return view('biens.edit', ['bien' => $bien] + $this->formData(auth()->user()));
    }

    public function update(Request $request, Bien $bien): RedirectResponse
    {
        $this->authorizeBien($request->user(), $bien);
        $data = $request->validate([
            ...$this->rules($bien),
            'commentaire_etat' => ['nullable', 'string', 'max:2000'],
            'justificatif_etat' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);
        $comment = $data['commentaire_etat'] ?? null;
        unset($data['commentaire_etat'], $data['justificatif_etat']);

        if ($data['etat'] !== $bien->etat && trim((string) $comment) === '') {
            return back()->withErrors([
                'commentaire_etat' => 'Un commentaire est requis lors d’un changement d’état.',
            ])->withInput();
        }
        if ($request->hasFile('justificatif_etat') && $data['etat'] === $bien->etat) {
            return back()->withErrors([
                'justificatif_etat' => 'Un justificatif doit accompagner un changement d’état.',
            ])->withInput();
        }
        $this->authorizeService($request, (int) $data['service_id']);

        $justificatif = null;
        try {
            DB::transaction(function () use ($request, $bien, $data, $comment, &$justificatif): void {
                $bien = Bien::query()->lockForUpdate()->findOrFail($bien->id);
                $this->authorizeBien($request->user(), $bien);
                $this->authorizeService($request, (int) $data['service_id']);

                $stateChanged = $data['etat'] !== $bien->etat;
                $bien->update($data);

                if ($stateChanged) {
                    $justificatif = $request->file('justificatif_etat')?->store('justificatifs/etats', 'local');
                    if ($request->hasFile('justificatif_etat') && ! $justificatif) {
                        throw new \RuntimeException('Impossible d’enregistrer le justificatif du changement d’état.');
                    }

                    HistoriqueEtat::query()
                        ->where('bien_id', $bien->id)
                        ->latest('id')
                        ->firstOrFail()
                        ->update([
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

        return redirect()->route('biens.show', $bien)->with('success', 'Le bien a été mis à jour.');
    }

    public function archive(Bien $bien): RedirectResponse
    {
        $this->authorizeBien(auth()->user(), $bien);
        $bien->delete();

        return redirect()->route('biens.index')->with('success', 'Le bien a été archivé.');
    }

    public function downloadHistoryEvidence(Request $request, Bien $bien, HistoriqueEtat $historique): StreamedResponse
    {
        $this->authorizeBien($request->user(), $bien);
        abort_unless($historique->bien_id === $bien->id && $historique->justificatif, 404);
        abort_unless(Storage::disk('local')->exists($historique->justificatif), 404);

        return Storage::disk('local')->download($historique->justificatif);
    }

    private function formData(User $user): array
    {
        return [
            'categories' => Categorie::query()->orderBy('nom')->get(),
            'services' => $this->servicesFor($user),
            'etats' => Bien::ETATS,
        ];
    }

    private function servicesFor(User $user)
    {
        return Service::query()
            ->when(
                ! $user->isNationalAdministrator(),
                fn ($query) => $user->service_id
                    ? $query->whereKey($user->service_id)
                    : $query->whereRaw('1 = 0'),
            )
            ->orderBy('nom')
            ->get();
    }

    private function authorizeBien(User $user, Bien $bien): void
    {
        abort_if(! $user->isNationalAdministrator() && $user->service_id !== $bien->service_id, 404);
    }

    private function authorizeService(Request $request, int $serviceId): void
    {
        abort_if(! $request->user()->isNationalAdministrator() && $request->user()->service_id !== $serviceId, 403);
    }

    private function rules(?Bien $bien = null): array
    {
        return [
            'numero_inventaire' => [
                'required',
                'string',
                'max:100',
                Rule::unique('biens', 'numero_inventaire')->ignore($bien?->id),
            ],
            'designation' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'date_acquisition' => ['nullable', 'date'],
            'valeur_acquisition' => ['nullable', 'numeric', 'min:0', 'max:9999999999999.99'],
            'source_financement' => ['nullable', 'string', 'max:255'],
            'categorie_id' => ['required', 'integer', 'exists:categories,id'],
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'localisation' => ['required', 'string', 'max:255'],
            'responsable' => ['nullable', 'string', 'max:255'],
            'etat' => ['required', 'string', Rule::in(Bien::ETATS)],
        ];
    }
}
