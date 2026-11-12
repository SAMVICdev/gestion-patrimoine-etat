<?php

namespace Tests\Feature\Biens;

use App\Features\Biens\Models\Bien;
use App\Features\Categories\Models\Categorie;
use App\Features\Services\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BienApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_manage_and_archive_biens(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'administrateur_national']));
        $categorie = Categorie::create(['nom' => 'Informatique']);
        $service = Service::create(['nom' => 'Direction des systèmes']);
        $data = $this->bienData($categorie->id, $service->id);

        $this->postJson('/api/biens', $data)
            ->assertCreated()
            ->assertJsonPath('data.numero_inventaire', 'INV-2026-001')
            ->assertJsonPath('data.categorie.nom', 'Informatique')
            ->assertJsonPath('data.service.nom', 'Direction des systèmes');

        $bien = Bien::firstOrFail();

        $this->getJson('/api/biens')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $bien->id);

        $this->getJson("/api/biens/{$bien->id}")
            ->assertOk()
            ->assertJsonPath('data.designation', 'Ordinateur portable');

        $this->patchJson("/api/biens/{$bien->id}", [
            'etat' => 'etat_moyen',
            'designation' => 'Ordinateur portable de service',
            'commentaire_etat' => 'Usure constatée lors du contrôle.',
        ])
            ->assertOk()
            ->assertJsonPath('data.etat', 'etat_moyen')
            ->assertJsonPath('data.designation', 'Ordinateur portable de service');

        $this->deleteJson("/api/biens/{$bien->id}")->assertNoContent();

        $this->assertSoftDeleted('biens', ['id' => $bien->id]);
        $this->getJson('/api/biens')->assertOk()->assertJsonCount(0, 'data');
        $this->getJson("/api/biens/{$bien->id}")->assertNotFound();
    }

    public function test_biens_endpoints_require_authentication(): void
    {
        $this->getJson('/api/biens')->assertUnauthorized();
        $this->postJson('/api/biens', [])->assertUnauthorized();
    }

    public function test_creation_validates_required_fields_relations_and_inventory_number_uniqueness(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'administrateur_national']));
        $categorie = Categorie::create(['nom' => 'Mobilier']);
        $service = Service::create(['nom' => 'Direction administrative']);
        $data = $this->bienData($categorie->id, $service->id);
        Bien::create($data);

        $this->postJson('/api/biens', [
            ...$data,
            'categorie_id' => 999,
            'service_id' => 999,
            'etat' => 'inconnu',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'numero_inventaire',
                'categorie_id',
                'service_id',
                'etat',
            ]);

        $this->postJson('/api/biens', [])->assertUnprocessable()
            ->assertJsonValidationErrors([
                'numero_inventaire',
                'designation',
                'categorie_id',
                'service_id',
                'localisation',
            ]);
    }

    public function test_index_can_filter_biens_by_search_and_state(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'administrateur_national']));
        $categorie = Categorie::create(['nom' => 'Véhicules']);
        $service = Service::create(['nom' => 'Garage central']);
        Bien::create($this->bienData($categorie->id, $service->id));
        Bien::create([
            ...$this->bienData($categorie->id, $service->id),
            'numero_inventaire' => 'INV-2026-002',
            'designation' => 'Camion de service',
            'etat' => 'etat_moyen',
        ]);

        $this->getJson('/api/biens?q=Camion&etat=etat_moyen')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.numero_inventaire', 'INV-2026-002');

        $this->getJson('/api/biens?per_page=101')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['per_page']);
    }

    private function bienData(int $categorieId, int $serviceId): array
    {
        return [
            'numero_inventaire' => 'INV-2026-001',
            'designation' => 'Ordinateur portable',
            'description' => 'Ordinateur affecté au service',
            'date_acquisition' => '2026-01-15',
            'valeur_acquisition' => 450000,
            'source_financement' => 'Budget national',
            'categorie_id' => $categorieId,
            'service_id' => $serviceId,
            'localisation' => 'Lomé',
            'responsable' => 'Agent du service',
            'etat' => 'bon_etat',
        ];
    }
}
