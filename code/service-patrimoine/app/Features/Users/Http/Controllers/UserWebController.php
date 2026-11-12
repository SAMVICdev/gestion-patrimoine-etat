<?php

namespace App\Features\Users\Http\Controllers;

use App\Features\Services\Models\Service;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CentralAuthClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserWebController extends Controller
{
    public function __construct(private readonly CentralAuthClient $centralAuth) {}

    public function index(): View
    {
        return view('users.index', [
            'users' => User::query()->with('service')->orderBy('name')->paginate(20),
            'roles' => User::ROLES,
            'newUserRoles' => ['gestionnaire', 'agent'],
            'services' => Service::query()->orderBy('nom')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(12)],
            'role' => ['required', Rule::in(['gestionnaire', 'agent'])],
            'service_id' => ['required', 'integer', 'exists:services,id'],
        ]);

        $response = $this->centralAuth->createUser(
            (string) $request->session()->get('central_auth_token'),
            $data,
        );
        if (! $response->successful()) {
            return back()
                ->withErrors($response->json('errors', ['email' => $response->json('message', 'Impossible de créer le compte centralisé.')]))
                ->withInput($request->except(['password', 'password_confirmation']));
        }

        $this->centralAuth->synchronizeUser($response->json('user') ?? []);

        return redirect()->route('utilisateurs.index')->with('success', 'Le compte a été créé.');
    }

    public function update(Request $request, User $utilisateur): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($utilisateur->id)],
            'role' => ['required', Rule::in(User::ROLES)],
            'service_id' => ['required_if:role,gestionnaire,agent', 'nullable', 'integer', 'exists:services,id'],
            'password' => ['nullable', 'confirmed', Password::min(12)],
        ]);

        $response = $this->centralAuth->updateUser(
            (string) $request->session()->get('central_auth_token'),
            $utilisateur->auth_user_id ?? abort(409, 'Ce compte n’est pas encore rattaché au service d’authentification.'),
            $data,
        );
        if (! $response->successful()) {
            return back()
                ->withErrors($response->json('errors', ['email' => $response->json('message', 'Impossible de mettre à jour le compte centralisé.')]))
                ->withInput($request->except(['password', 'password_confirmation']));
        }

        $updated = $this->centralAuth->synchronizeUser($response->json('user') ?? []);
        if (isset($data['password'])) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('success', 'Le mot de passe a été changé. Reconnectez-vous.');
        }

        return redirect()->route('utilisateurs.index')->with('success', 'Le compte '.$updated->name.' a été mis à jour.');
    }
}
