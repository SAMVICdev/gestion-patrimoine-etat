<?php

namespace App\Features\Users\Http\Controllers;

use App\Features\Services\Models\Service;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserWebController extends Controller
{
    public function index(): View
    {
        return view('users.index', [
            'users' => User::query()->with('service')->orderBy('name')->paginate(20),
            'roles' => User::ROLES,
            'services' => Service::query()->orderBy('nom')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(12)],
            'role' => ['required', Rule::in(User::ROLES)],
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
        ]);

        User::create($data);

        return redirect()->route('utilisateurs.index')->with('success', 'Le compte a été créé.');
    }

    public function update(Request $request, User $utilisateur): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($utilisateur->id)],
            'role' => ['required', Rule::in(User::ROLES)],
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
            'password' => ['nullable', 'confirmed', Password::min(12)],
        ]);

        if ($utilisateur->is($request->user()) && $data['role'] !== 'administrateur_national') {
            abort(422, 'Vous ne pouvez pas retirer votre propre rôle administrateur national.');
        }

        if (isset($data['password'])) {
            $utilisateur->password = $data['password'];
        }

        unset($data['password']);
        $utilisateur->update($data);

        return redirect()->route('utilisateurs.index')->with('success', 'Le compte a été mis à jour.');
    }
}
