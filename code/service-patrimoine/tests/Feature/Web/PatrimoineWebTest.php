<?php

namespace Tests\Feature\Web;

use App\Features\Biens\Models\Bien;
use App\Features\Categories\Models\Categorie;
use App\Features\Services\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PatrimoineWebTest extends TestCase
{
    use RefreshDatabase;

    public function test_web_navigation_route_names_resolve_to_web_pages(): void
    {
        $this->assertSame('/biens', route('biens.index', [], false));
        $this->assertSame('/categories', route('categories.index', [], false));
        $this->assertSame('/services', route('services.index', [], false));
    }

    public function test_agent_sees_read_only_actions_in_web_interfaces(): void
    {
        $service = Service::create(['nom' => 'Service utilisateur']);
        $categorie = Categorie::create(['nom' => 'Informatique']);
        $bien = Bien::create([
            'numero_inventaire' => 'PAT-AGENT-001',
            'designation' => 'Ordinateur de service',
            'categorie_id' => $categorie->id,
            'service_id' => $service->id,
            'localisation' => 'Lomé',
            'etat' => 'bon_etat',
        ]);
        $this->signInAs(User::factory()->create([
            'role' => 'agent',
            'service_id' => $service->id,
        ]));

        $this->get('/tableau-de-bord')
            ->assertOk()
            ->assertDontSee(route('biens.create', [], false))
            ->assertDontSee(route('categories.index', [], false));
        $this->get('/biens')
            ->assertOk()
            ->assertDontSee('Enregistrer un bien')
            ->assertDontSee(route('biens.create', [], false));
        $this->get(route('biens.show', $bien))
            ->assertOk()
            ->assertDontSee('Modifier')
            ->assertDontSee('Archiver');
        $this->get('/rapports')
            ->assertOk()
            ->assertDontSee(route('rapports.export', ['format' => 'csv'], false));
    }

    public function test_expired_central_token_redirects_and_auth_service_outage_fails_closed(): void
    {
        $profileUrl = rtrim(config('services.central_auth.url'), '/').'/api/user';
        Http::fake([
            $profileUrl => Http::sequence()
                ->push(['message' => 'Unauthenticated.'], 401)
                ->push([], 503),
        ]);
        $this->withSession(['central_auth_token' => 'revoked-token'])
            ->get('/tableau-de-bord')
            ->assertRedirect(route('login'));

        $this->withSession(['central_auth_token' => 'valid-looking-token'])
            ->get('/tableau-de-bord')
            ->assertStatus(503);
    }

    public function test_guest_is_redirected_to_login_and_can_sign_in_and_sign_out(): void
    {
        $this->get('/tableau-de-bord')->assertRedirect(route('login'));
        $this->get('/connexion')->assertOk()->assertSee('Bienvenue.');

        $user = User::factory()->create([
            'email' => 'gestionnaire@example.test',
            'password' => 'secret-password',
        ]);
        $identity = [
            'id' => 501,
            'name' => $user->name,
            'email' => $user->email,
            'role' => 'gestionnaire',
            'service_id' => null,
        ];
        Http::fake([
            rtrim(config('services.central_auth.url'), '/').'/api/login' => Http::sequence()
                ->push(['message' => 'Identifiants invalides.'], 422)
                ->push(['token' => 'valid-central-token', 'user' => $identity]),
            rtrim(config('services.central_auth.url'), '/').'/api/user' => Http::response(['user' => $identity]),
            rtrim(config('services.central_auth.url'), '/').'/api/logout' => Http::response(['message' => 'Session terminée.']),
        ]);

        $this->post('/connexion', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->post('/connexion', [
            'email' => $user->email,
            'password' => 'secret-password',
        ])->assertRedirect(route('dashboard'));

        $this->get('/tableau-de-bord')->assertOk()->assertSee('Le patrimoine public');

        $this->post('/deconnexion')->assertRedirect(route('login'));
        $this->get('/tableau-de-bord')->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_manage_inventory_and_referentials(): void
    {
        $this->signInAs(User::factory()->create(['role' => 'administrateur_national']));

        $this->post('/categories', ['nom' => 'Informatique'])
            ->assertRedirect(route('categories.index'));
        $this->post('/services', ['nom' => 'Direction technique'])
            ->assertRedirect(route('services.index'));

        $categorie = Categorie::firstOrFail();
        $service = Service::firstOrFail();
        $data = [
            'numero_inventaire' => 'PAT-2026-001',
            'designation' => 'Ordinateur de bureau',
            'categorie_id' => $categorie->id,
            'service_id' => $service->id,
            'localisation' => 'Lomé',
            'etat' => 'bon_etat',
            'valeur_acquisition' => 275000,
        ];

        $this->get('/biens/create')->assertOk()->assertSee('Enregistrer un bien');
        $this->post('/biens', $data)->assertRedirect();
        $bien = Bien::firstOrFail();

        $this->get('/biens')->assertOk()->assertSee('PAT-2026-001')->assertSee('Ordinateur de bureau');
        $this->get("/biens/{$bien->id}")->assertOk()->assertSee('Fiche patrimoniale');
        $this->get('/tableau-de-bord')->assertOk()->assertSee('275 000');

        $this->put("/biens/{$bien->id}", [
            ...$data,
            'etat' => 'etat_moyen',
            'commentaire_etat' => 'Usure constatée lors du contrôle.',
        ])
            ->assertRedirect(route('biens.show', $bien));
        $this->assertDatabaseHas('biens', ['id' => $bien->id, 'etat' => 'etat_moyen']);

        $this->post("/biens/{$bien->id}/archiver")->assertRedirect(route('biens.index'));
        $this->assertSoftDeleted('biens', ['id' => $bien->id]);
    }
}
