<?php

namespace App\Features\Services\Http\Controllers;

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
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Service::create($request->validate([
            'nom' => ['required', 'string', 'max:255', 'unique:services,nom'],
            'description' => ['nullable', 'string'],
        ]));

        return redirect()->route('services.index')->with('success', 'Le service a été créé.');
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $service->update($request->validate([
            'nom' => ['required', 'string', 'max:255', Rule::unique('services', 'nom')->ignore($service->id)],
            'description' => ['nullable', 'string'],
        ]));

        return redirect()->route('services.index')->with('success', 'Le service a été mis à jour.');
    }
}
