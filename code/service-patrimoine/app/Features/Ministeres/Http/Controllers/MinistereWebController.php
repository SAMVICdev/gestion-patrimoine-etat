<?php

namespace App\Features\Ministeres\Http\Controllers;

use App\Features\Ministeres\Models\Ministere;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MinistereWebController extends Controller
{
    public function index(): View
    {
        return view('ministeres.index', [
            'ministeres' => Ministere::query()->withCount('services')->orderBy('nom')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Ministere::create($request->validate([
            'nom' => ['required', 'string', 'max:255', 'unique:ministeres,nom'],
            'code' => ['nullable', 'string', 'max:30', 'unique:ministeres,code'],
            'description' => ['nullable', 'string'],
        ]));

        return redirect()->route('ministeres.index')->with('success', 'Le ministère a été créé.');
    }

    public function update(Request $request, Ministere $ministere): RedirectResponse
    {
        $ministere->update($request->validate([
            'nom' => ['required', 'string', 'max:255', Rule::unique('ministeres', 'nom')->ignore($ministere->id)],
            'code' => ['nullable', 'string', 'max:30', Rule::unique('ministeres', 'code')->ignore($ministere->id)],
            'description' => ['nullable', 'string'],
        ]));

        return redirect()->route('ministeres.index')->with('success', 'Le ministère a été mis à jour.');
    }
}
