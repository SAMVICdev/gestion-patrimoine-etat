<?php

namespace App\Features\Parametres\Http\Controllers;

use App\Features\Parametres\Models\ParametresPlateforme;
use App\Http\Controllers\Controller;
use App\Services\CentralAuthClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ParametresWebController extends Controller
{
    public function __construct(private readonly CentralAuthClient $centralAuth) {}

    /**
     * Affiche les paramètres de la plateforme (admin national uniquement).
     */
    public function index(): View
    {
        return view('parametres.index', [
            'parametres' => ParametresPlateforme::query()->orderBy('cle')->get(),
        ]);
    }

    /**
     * Enregistre les paramètres de la plateforme.
     */
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nom_organisme'      => ['required', 'string', 'max:255'],
            'pays'               => ['required', 'string', 'max:100'],
            'devise'             => ['required', 'string', 'max:20'],
            'email_contact'      => ['nullable', 'email', 'max:255'],
            'telephone_contact'  => ['nullable', 'string', 'max:50'],
            'adresse'            => ['nullable', 'string', 'max:500'],
            'prefixe_inventaire' => ['required', 'string', 'max:10', 'alpha'],
            'annee_exercice'     => ['required', 'digits:4', 'integer', 'min:2000', 'max:2100'],
        ]);

        foreach ($data as $cle => $valeur) {
            ParametresPlateforme::set($cle, (string) $valeur);
        }

        return redirect()->route('parametres.index')->with('success', 'Les paramètres ont été enregistrés.');
    }

    /**
     * Affiche le formulaire de profil de l'utilisateur connecté.
     */
    public function profil(): View
    {
        return view('parametres.profil', [
            'user' => auth()->user(),
        ]);
    }

    /**
     * Enregistre les modifications du profil de l'utilisateur connecté.
     */
    public function updateProfil(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'confirmed', Password::min(12)
                ->mixedCase()
                ->numbers(),
            ],
        ]);

        $payload = ['name' => $data['name']];
        if (! empty($data['password'])) {
            $payload['password'] = $data['password'];
        }

        $response = $this->centralAuth->updateUser(
            (string) $request->session()->get('central_auth_token'),
            $user->auth_user_id ?? abort(409, 'Ce compte n\'est pas rattaché au service d\'authentification.'),
            $payload,
        );

        if (! $response->successful()) {
            return back()
                ->withErrors($response->json('errors', ['name' => $response->json('message', 'Impossible de mettre à jour le profil.')]))
                ->withInput($request->except(['password', 'password_confirmation']));
        }

        $this->centralAuth->synchronizeUser($response->json('user') ?? []);

        if (! empty($data['password'])) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('success', 'Mot de passe modifié. Reconnectez-vous.');
        }

        return redirect()->route('parametres.profil')->with('success', 'Votre profil a été mis à jour.');
    }
}
