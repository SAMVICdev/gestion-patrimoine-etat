<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\CentralAuthClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    public function __construct(private readonly CentralAuthClient $centralAuth) {}

    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $response = $this->centralAuth->sendPasswordResetLink($data['email']);
        if (! $response->successful()) {
            return back()
                ->withErrors(['email' => $response->json('message') ?? 'Impossible d’envoyer le lien de réinitialisation.'])
                ->withInput();
        }

        return back()->with('status', 'Si un compte existe avec cette adresse, un lien de réinitialisation a été envoyé.');
    }
}
