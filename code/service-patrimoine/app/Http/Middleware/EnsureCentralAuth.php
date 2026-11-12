<?php

namespace App\Http\Middleware;

use App\Services\CentralAuthClient;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureCentralAuth
{
    public function __construct(private readonly CentralAuthClient $centralAuth) {}

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->hasSession()
            ? $request->session()->get('central_auth_token')
            : null;
        $token ??= $request->bearerToken();

        if (! is_string($token) || $token === '') {
            return $this->unauthenticated($request);
        }

        $identity = $this->centralAuth->currentUser($token);
        if (! $identity) {
            if ($request->hasSession()) {
                Auth::logout();
                $request->session()->forget('central_auth_token');
            }

            return $this->unauthenticated($request);
        }

        $user = $this->centralAuth->synchronizeUser($identity);
        Auth::setUser($user);
        $request->setUserResolver(fn () => $user);

        return $next($request);
    }

    private function unauthenticated(Request $request): Response
    {
        if ($request->expectsJson()) {
            abort(401, 'Authentification requise.');
        }

        return redirect()->guest(route('login'));
    }
}
