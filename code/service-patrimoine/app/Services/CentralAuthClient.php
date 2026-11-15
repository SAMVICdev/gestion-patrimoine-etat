<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use UnexpectedValueException;

class CentralAuthClient
{
    public function login(string $email, string $password): Response
    {
        return $this->send(fn (PendingRequest $request) => $request->post('/api/login', [
            'email' => $email,
            'password' => $password,
        ]));
    }

    public function sendPasswordResetLink(string $email): Response
    {
        return $this->send(fn (PendingRequest $request) => $request->post('/api/password/email', [
            'email' => $email,
        ]));
    }

    public function resetPassword(array $data): Response
    {
        return $this->send(fn (PendingRequest $request) => $request->post('/api/password/reset', $data));
    }

    public function currentUser(string $token): ?array
    {
        $response = $this->send(fn (PendingRequest $request) => $request->withToken($token)->get('/api/user'));
        if ($response->unauthorized() || $response->forbidden()) {
            return null;
        }

        $this->ensureAvailable($response);
        $user = $response->json('user');
        if (! is_array($user)) {
            throw new UnexpectedValueException('Le service d’authentification a renvoyé un profil invalide.');
        }

        return $user;
    }

    public function createUser(string $token, array $data): Response
    {
        return $this->send(fn (PendingRequest $request) => $request->withToken($token)->post('/api/register', $data));
    }

    public function updateUser(string $token, int $authUserId, array $data): Response
    {
        return $this->send(fn (PendingRequest $request) => $request->withToken($token)->put('/api/users/'.$authUserId, $data));
    }

    public function logout(string $token): void
    {
        $response = $this->send(fn (PendingRequest $request) => $request->withToken($token)->post('/api/logout'));
        $this->ensureAvailable($response);
    }

    public function synchronizeUser(array $data): User
    {
        if (! isset($data['id'], $data['name'], $data['email'], $data['role'])
            || ! is_numeric($data['id'])
            || ! in_array($data['role'], User::ROLES, true)) {
            throw new UnexpectedValueException('Le service d’authentification a renvoyé une identité invalide.');
        }

        $user = User::query()->where('auth_user_id', (int) $data['id'])->first();
        if (! $user) {
            $user = User::query()->where('email', $data['email'])->first();
        }

        if (! $user) {
            $user = new User([
                'password' => Hash::make(Str::random(64)),
            ]);
        }

        if ($user->auth_user_id !== null && $user->auth_user_id !== (int) $data['id']) {
            abort(409, 'Cette adresse e-mail est associée à une autre identité centralisée.');
        }

        $user->fill([
            'auth_user_id' => (int) $data['id'],
            'name' => $data['name'],
            'email' => mb_strtolower($data['email']),
            'role' => $data['role'],
            'service_id' => $data['service_id'] ?? null,
        ]);
        $user->save();

        return $user;
    }

    private function send(callable $callback): Response
    {
        try {
            $response = $callback(
                Http::baseUrl(rtrim((string) config('services.central_auth.url'), '/'))
                    ->acceptJson()
                    ->timeout(5),
            );
        } catch (ConnectionException $exception) {
            throw new ServiceUnavailableHttpException(null, 'Le service d’authentification est indisponible.', $exception);
        }

        $this->ensureAvailable($response);

        return $response;
    }

    private function ensureAvailable(Response $response): void
    {
        if ($response->serverError()) {
            throw new ServiceUnavailableHttpException(null, 'Le service d’authentification est indisponible.');
        }
    }
}
