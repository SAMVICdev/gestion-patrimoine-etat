<?php

namespace App\Features\Biens\Http\Controllers;

use App\Features\Biens\Models\Bien;
use App\Features\Categories\Models\Categorie;
use App\Features\Services\Models\Service;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

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
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('biens.index', [
            'biens' => $biens,
            'categories' => Categorie::query()->orderBy('nom')->get(),
            'services' => Service::query()->orderBy('nom')->get(),
            'etats' => Bien::ETATS,
        ]);
    }

    public function create(): View
    {
        return view('biens.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $bien = Bien::create($request->validate($this->rules()));

        return redirect()->route('biens.show', $bien)->with('success', 'Le bien a été enregistré.');
    }

    public function show(Bien $bien): View
    {
        return view('biens.show', ['bien' => $bien->load(['categorie', 'service'])]);
    }

    public function edit(Bien $bien): View
    {
        return view('biens.edit', ['bien' => $bien] + $this->formData());
    }

    public function update(Request $request, Bien $bien): RedirectResponse
    {
        $bien->update($request->validate($this->rules($bien)));

        return redirect()->route('biens.show', $bien)->with('success', 'Le bien a été mis à jour.');
    }

    public function archive(Bien $bien): RedirectResponse
    {
        $bien->delete();

        return redirect()->route('biens.index')->with('success', 'Le bien a été archivé.');
    }

    private function formData(): array
    {
        return [
            'categories' => Categorie::query()->orderBy('nom')->get(),
            'services' => Service::query()->orderBy('nom')->get(),
            'etats' => Bien::ETATS,
        ];
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
