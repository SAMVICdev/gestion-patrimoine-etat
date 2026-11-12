<?php

namespace Tests\Feature\Referentiels;

use App\Features\Categories\Models\Categorie;
use App\Features\Services\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReferentielApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_manage_categories_and_services(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'administrateur_national']));

        $this->postJson('/api/categories', [
            'nom' => 'Informatique',
            'description' => 'Materiel et equipements informatiques',
        ])
            ->assertCreated()
            ->assertJsonPath('data.nom', 'Informatique');

        $this->postJson('/api/services', [
            'nom' => 'Direction des systemes',
        ])
            ->assertCreated()
            ->assertJsonPath('data.nom', 'Direction des systemes');

        $categorie = Categorie::firstOrFail();
        $service = Service::firstOrFail();

        $this->getJson('/api/categories')->assertOk()->assertJsonCount(1, 'data');
        $this->getJson("/api/categories/{$categorie->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $categorie->id);
        $this->patchJson("/api/categories/{$categorie->id}", [
            'nom' => 'Materiel informatique',
        ])->assertOk()->assertJsonPath('data.nom', 'Materiel informatique');

        $this->getJson('/api/services')->assertOk()->assertJsonCount(1, 'data');
        $this->getJson("/api/services/{$service->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $service->id);
        $this->patchJson("/api/services/{$service->id}", [
            'description' => 'Service central',
        ])->assertOk()->assertJsonPath('data.description', 'Service central');
    }

    public function test_referential_endpoints_require_authentication(): void
    {
        $this->getJson('/api/categories')->assertUnauthorized();
        $this->postJson('/api/categories', [])->assertUnauthorized();
        $this->getJson('/api/services')->assertUnauthorized();
        $this->postJson('/api/services', [])->assertUnauthorized();
    }

    public function test_category_and_service_names_must_be_unique_and_required(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'administrateur_national']));
        Categorie::create(['nom' => 'Mobilier']);
        Service::create(['nom' => 'Service central']);

        $this->postJson('/api/categories', ['nom' => 'Mobilier'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['nom']);
        $this->postJson('/api/services', ['nom' => 'Service central'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['nom']);
        $this->postJson('/api/categories', [])->assertUnprocessable()
            ->assertJsonValidationErrors(['nom']);
        $this->postJson('/api/services', [])->assertUnprocessable()
            ->assertJsonValidationErrors(['nom']);
    }

    public function test_only_authorized_roles_can_write_referentials(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'agent']));
        $this->postJson('/api/categories', ['nom' => 'Informatique'])->assertForbidden();
        $this->postJson('/api/services', ['nom' => 'Service central'])->assertForbidden();

        $this->actingAs(User::factory()->create(['role' => 'gestionnaire']));
        $this->postJson('/api/categories', ['nom' => 'Informatique'])->assertCreated();
        $this->postJson('/api/services', ['nom' => 'Service central'])->assertForbidden();
    }
}
