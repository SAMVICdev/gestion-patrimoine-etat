<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_get_profile_and_revoke_token(): void
    {
        $user = User::factory()->create([
            'email' => 'agent@example.test',
            'password' => 'SafePassword123',
            'role' => 'agent',
            'service_id' => 14,
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'SafePassword123',
        ])->assertOk()
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonPath('user.role', 'agent')
            ->assertJsonPath('user.service_id', 14);

        $token = $response->json('token');
        $this->withToken($token)->getJson('/api/user')
            ->assertOk()
            ->assertJsonPath('user.email', $user->email);
        $this->withToken($token)->postJson('/api/logout')->assertOk();
        $this->assertSame(0, $user->tokens()->count());
    }

    public function test_login_uses_a_generic_error_for_invalid_credentials(): void
    {
        User::factory()->create(['email' => 'agent@example.test']);

        $this->postJson('/api/login', [
            'email' => 'agent@example.test',
            'password' => 'wrong-password',
        ])->assertUnprocessable()
            ->assertJsonPath('message', 'Identifiants invalides.');
    }

    public function test_only_national_administrators_can_create_and_manage_accounts(): void
    {
        $manager = User::factory()->create(['role' => 'gestionnaire', 'service_id' => 4]);
        $this->actingAs($manager)
            ->postJson('/api/register', $this->newUserData())
            ->assertForbidden();

        $admin = User::factory()->create(['role' => 'administrateur_national']);
        $this->actingAs($admin)
            ->postJson('/api/register', $this->newUserData())
            ->assertCreated()
            ->assertJsonPath('user.role', 'agent')
            ->assertJsonPath('user.service_id', 7);
    }

    public function test_last_national_administrator_cannot_be_demoted(): void
    {
        $admin = User::factory()->create(['role' => 'administrateur_national']);

        $this->actingAs($admin)
            ->putJson("/api/users/{$admin->id}", [
                'name' => $admin->name,
                'email' => $admin->email,
                'role' => 'gestionnaire',
                'service_id' => 7,
            ])
            ->assertUnprocessable();
        $this->assertSame('administrateur_national', $admin->fresh()->role);
    }

    public function test_admin_can_change_another_users_password_and_old_tokens_are_revoked(): void
    {
        $admin = User::factory()->create(['role' => 'administrateur_national']);
        $user = User::factory()->create(['role' => 'agent', 'service_id' => 7]);
        $user->createToken('old-session');

        $this->actingAs($admin)
            ->putJson("/api/users/{$user->id}", [
                'name' => $user->name,
                'email' => $user->email,
                'role' => 'agent',
                'service_id' => 7,
                'password' => 'UpdatedPassword123',
                'password_confirmation' => 'UpdatedPassword123',
            ])
            ->assertOk();

        $this->assertSame(0, $user->tokens()->count());
        $this->assertTrue(Hash::check('UpdatedPassword123', $user->fresh()->password));
    }

    private function newUserData(): array
    {
        return [
            'name' => 'Agent de contrôle',
            'email' => 'controle@example.test',
            'password' => 'StrongPassword123',
            'password_confirmation' => 'StrongPassword123',
            'role' => 'agent',
            'service_id' => 7,
        ];
    }
}
