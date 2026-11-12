<?php

namespace App\Features\Categories\Http\Controllers;

use App\Features\Categories\Models\Categorie;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategorieWebController extends Controller
{
    public function index(): View
    {
        return view('categories.index', [
            'categories' => Categorie::query()->withCount('biens')->orderBy('nom')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Categorie::create($request->validate([
            'nom' => ['required', 'string', 'max:255', 'unique:categories,nom'],
            'description' => ['nullable', 'string'],
        ]));

        return redirect()->route('categories.index')->with('success', 'La catégorie a été créée.');
    }

    public function update(Request $request, Categorie $categorie): RedirectResponse
    {
        $categorie->update($request->validate([
            'nom' => ['required', 'string', 'max:255', Rule::unique('categories', 'nom')->ignore($categorie->id)],
            'description' => ['nullable', 'string'],
        ]));

        return redirect()->route('categories.index')->with('success', 'La catégorie a été mise à jour.');
    }
}
