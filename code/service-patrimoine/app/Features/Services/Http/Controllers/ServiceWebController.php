<?php

namespace App\Features\Services\Http\Controllers;

use App\Features\Ministeres\Models\Ministere;
use App\Features\Services\Models\Service;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ServiceWebController extends Controller
{
    public function index(): View
    {
        return view('services.index', [
            'services' => Service::query()->withCount('biens')->orderBy('nom')->get(),
            'ministeres' => Ministere::query()->orderBy('nom')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Service::create($request->validate([
            'nom' => ['required', 'string', 'max:255', 'unique:services,nom'],
            'description' => ['nullable', 'string'],
            'ministere_id' => ['nullable', 'integer', 'exists:ministeres,id'],
        ]));

        return redirect()->route('services.index')->with('success', 'Le service a été créé.');
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $service->update($request->validate([
            'nom' => ['required', 'string', 'max:255', Rule::unique('services', 'nom')->ignore($service->id)],
            'description' => ['nullable', 'string'],
            'ministere_id' => ['nullable', 'integer', 'exists:ministeres,id'],
        ]));

        return redirect()->route('services.index')->with('success', 'Le service a été mis à jour.');
    }
}
