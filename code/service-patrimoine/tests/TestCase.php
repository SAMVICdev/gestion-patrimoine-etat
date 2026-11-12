<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Http;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    protected function signInAs(User $user): static
    {
        $identity = [
            'id' => $user->auth_user_id ?? $user->id + 10000,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'service_id' => $user->service_id,
        ];
        Http::fake([
            rtrim(config('services.central_auth.url'), '/').'/api/user' => Http::response(['user' => $identity]),
        ]);

        return $this->actingAs($user)
            ->withSession(['central_auth_token' => 'test-central-token'])
            ->withToken('test-central-token');
    }
}
