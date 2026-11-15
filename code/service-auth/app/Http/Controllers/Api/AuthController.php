<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password as PasswordRule;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->merge(['email' => mb_strtolower(trim((string) $request->input('email')))]);
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);
        $user = User::query()->where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Identifiants invalides.'], 422);
        }

        $token = $user->createToken(
            'patrimoine',
            ['*'],
            now()->addMinutes((int) config('sanctum.expiration', 60)),
        )->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $this->userData($user),
        ]);
    }

    public function sendResetLinkEmail(Request $request): JsonResponse
    {
        $request->merge(['email' => mb_strtolower(trim((string) $request->input('email')))]);
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        Password::sendResetLink($request->only('email'));

        return response()->json([
            'message' => 'Si un compte existe avec cette adresse, un lien de réinitialisation a été envoyé.',
        ]);
    }

    public function reset(Request $request): JsonResponse
    {
        $request->merge(['email' => mb_strtolower(trim((string) $request->input('email')))]);
        $data = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'confirmed', PasswordRule::min(12)->letters()->mixedCase()->numbers()],
        ]);

        $status = Password::reset($data, function (User $user, string $password): void {
            $user->password = $password;
            $user->setRememberToken(Str::random(60));
            $user->save();
            $user->tokens()->delete();
        });

        if ($status !== Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Ce lien de réinitialisation est invalide ou a expiré.',
            ], 422);
        }

        return response()->json([
            'message' => 'Mot de passe réinitialisé. Vous pouvez maintenant vous connecter.',
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Session terminée.']);
    }

    public function user(Request $request): JsonResponse
    {
        return response()->json(['user' => $this->userData($request->user())]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->merge(['email' => mb_strtolower(trim((string) $request->input('email')))]);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', PasswordRule::min(12)->letters()->mixedCase()->numbers()],
            'role' => ['required', Rule::in(['gestionnaire', 'agent'])],
            'service_id' => ['required', 'integer', 'min:1'],
        ]);
        $user = User::query()->create($data);

        return response()->json(['user' => $this->userData($user)], 201);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $request->merge(['email' => mb_strtolower(trim((string) $request->input('email')))]);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in(User::ROLES)],
            'service_id' => ['nullable', 'integer', 'min:1'],
            'password' => ['nullable', 'confirmed', PasswordRule::min(12)->letters()->mixedCase()->numbers()],
        ]);

        if ($user->is($request->user()) && $data['role'] !== 'administrateur_national') {
            return response()->json(['message' => 'Vous ne pouvez pas retirer votre propre rôle administrateur national.'], 422);
        }

        if ($user->role === 'administrateur_national' && $data['role'] !== 'administrateur_national'
            && User::query()->where('role', 'administrateur_national')->count() <= 1) {
            return response()->json(['message' => 'La plateforme doit conserver au moins un administrateur national.'], 422);
        }

        if (isset($data['password'])) {
            $user->password = $data['password'];
            unset($data['password']);
            $user->tokens()->delete();
        }

        $user->update($data);

        return response()->json(['user' => $this->userData($user->fresh())]);
    }

    private function userData(User $user): array
    {
        return $user->only(['id', 'name', 'email', 'role', 'service_id']);
    }
}
