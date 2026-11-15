<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\CentralAuthClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ResetPasswordController extends Controller
{
    public function __construct(private readonly CentralAuthClient $centralAuth) {}

    public function create(Request $request): View
    {
        return view('auth.reset-password', [
            'token' => (string) $request->query('token', ''),
            'email' => (string) $request->query('email', ''),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Password::min(12)->mixedCase()->numbers()],
        ]);

        $response = $this->centralAuth->resetPassword($data);
        if (! $response->successful()) {
            return back()
                ->withErrors(['email' => $response->json('message') ?? 'Ce lien de réinitialisation est invalide ou a expiré.'])
                ->withInput($request->except('password', 'password_confirmation'));
        }

        return redirect()->route('login')->with('status', 'Mot de passe réinitialisé. Connectez-vous avec votre nouveau mot de passe.');
    }
}
