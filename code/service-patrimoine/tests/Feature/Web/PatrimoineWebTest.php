<?php

namespace Tests\Feature\Web;

use App\Features\Biens\Models\Bien;
use App\Features\Categories\Models\Categorie;
use App\Features\Services\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatrimoineWebTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_and_can_sign_in_and_sign_out(): void
    {
        $this->get('/tableau-de-bord')->assertRedirect(route('login'));
        $this->get('/connexion')->assertOk()->assertSee('Bienvenue.');

        $user = User::factory()->create([
            'email' => 'gestionnaire@example.test',
            'password' => 'secret-password',
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
        $this->actingAs(User::factory()->create());

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

        $this->put("/biens/{$bien->id}", [...$data, 'etat' => 'etat_moyen'])
            ->assertRedirect(route('biens.show', $bien));
        $this->assertDatabaseHas('biens', ['id' => $bien->id, 'etat' => 'etat_moyen']);

        $this->post("/biens/{$bien->id}/archiver")->assertRedirect(route('biens.index'));
        $this->assertSoftDeleted('biens', ['id' => $bien->id]);
    }
}
