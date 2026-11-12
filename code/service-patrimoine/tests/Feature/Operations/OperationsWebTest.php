<?php

namespace Tests\Feature\Operations;

use App\Features\Biens\Models\Bien;
use App\Features\Categories\Models\Categorie;
use App\Features\Inventaires\Models\CampagneInventaire;
use App\Features\Services\Models\Service;
use App\Features\Transferts\Models\Transfert;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperationsWebTest extends TestCase
{
    use RefreshDatabase;

    public function test_transfer_requires_approval_and_moves_asset_to_destination_service(): void
    {
        $source = Service::create(['nom' => 'Service source']);
        $destination = Service::create(['nom' => 'Service destinataire']);
        $bien = $this->createBien($source);
        $administrateur = User::factory()->create(['role' => 'administrateur_national']);

        $this->signInAs($administrateur)
            ->post('/transferts', [
                'bien_id' => $bien->id,
                'service_destination_id' => $destination->id,
                'localisation_destination' => 'Lomé, bâtiment B',
                'motif' => 'Réaffectation du service.',
            ])
            ->assertRedirect();

        $transfert = Transfert::firstOrFail();
        $this->assertSame('en_attente', $transfert->statut);
        $this->assertSame($source->id, $bien->fresh()->service_id);

        $this->post('/transferts', [
            'bien_id' => $bien->id,
            'service_destination_id' => $destination->id,
            'localisation_destination' => 'Lomé, bâtiment B',
            'motif' => 'Deuxième demande.',
        ])->assertStatus(409);

        $this->post("/transferts/{$transfert->id}/approuver", [])
            ->assertRedirect();

        $this->assertDatabaseHas('transferts', [
            'id' => $transfert->id,
            'statut' => 'approuve',
        ]);
        $this->assertDatabaseHas('biens', [
            'id' => $bien->id,
            'service_id' => $destination->id,
            'localisation' => 'Lomé, bâtiment B',
        ]);
    }

    public function test_inventory_campaign_is_scoped_and_closes_after_recording_findings(): void
    {
        $service = Service::create(['nom' => 'Service inventorié']);
        $autreService = Service::create(['nom' => 'Autre service']);
        $bien = $this->createBien($service);
        $gestionnaire = User::factory()->create([
            'role' => 'gestionnaire',
            'service_id' => $service->id,
        ]);

        $this->signInAs($gestionnaire)
            ->post('/inventaires', [
                'titre' => 'Inventaire annuel',
                'service_id' => $autreService->id,
            ])
            ->assertRedirect();

        $campagne = CampagneInventaire::firstOrFail();
        $this->assertSame($service->id, $campagne->service_id);

        $this->post("/inventaires/{$campagne->id}/constats", [
            'bien_id' => $bien->id,
            'resultat' => 'introuvable',
            'commentaire' => 'Le bien est introuvable lors du contrôle.',
        ])->assertRedirect();

        $this->assertDatabaseHas('constats_inventaire', [
            'campagne_id' => $campagne->id,
            'bien_id' => $bien->id,
            'agent_id' => $gestionnaire->id,
        ]);
        $this->assertDatabaseHas('biens', [
            'id' => $bien->id,
            'etat' => 'introuvable',
        ]);

        $this->post("/inventaires/{$campagne->id}/cloturer")
            ->assertRedirect();

        $this->assertDatabaseHas('campagnes_inventaire', [
            'id' => $campagne->id,
            'statut' => 'cloturee',
        ]);
        $this->post("/inventaires/{$campagne->id}/constats", [
            'bien_id' => $bien->id,
            'resultat' => 'present',
        ])->assertStatus(409);
    }

    private function createBien(Service $service): Bien
    {
        return Bien::create([
            'numero_inventaire' => 'INV-'.fake()->unique()->numerify('######'),
            'designation' => 'Ordinateur de service',
            'categorie_id' => Categorie::create(['nom' => 'Informatique'])->id,
            'service_id' => $service->id,
            'localisation' => 'Lomé, bâtiment A',
            'etat' => 'bon_etat',
        ]);
    }
}
