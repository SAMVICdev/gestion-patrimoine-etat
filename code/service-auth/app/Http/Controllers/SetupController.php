<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SetupController extends Controller
{
    public function show(): View
    {
        $this->guard();

        return view('setup');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->guard();

        $data = $request->validate([
            'token' => ['required', 'string'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(12)->letters()->mixedCase()->numbers()],
        ]);

        if (! hash_equals((string) config('app.setup_token'), $data['token'])) {
            return back()->withErrors(['token' => 'Jeton de configuration invalide.'])->withInput();
        }

        User::query()->create([
            'name' => $data['name'],
            'email' => mb_strtolower($data['email']),
            'password' => Hash::make($data['password']),
            'role' => 'administrateur_national',
        ]);

        return redirect()->route('setup.done');
    }

    public function done(): View
    {
        if (! User::query()->where('role', 'administrateur_national')->exists()) {
            throw new NotFoundHttpException;
        }

        return view('setup-done');
    }

    private function guard(): void
    {
        $token = config('app.setup_token');

        if (! is_string($token) || $token === '') {
            throw new NotFoundHttpException;
        }

        if (User::query()->where('role', 'administrateur_national')->exists()) {
            throw new NotFoundHttpException;
        }
    }
}
