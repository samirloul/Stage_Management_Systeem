<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Acceptatietests voor bedrijven CRUD.
 *
 * Controleert dat een admin bedrijven kan aanmaken, bewerken en verwijderen,
 * en dat validatieregels correct worden gehandhaafd.
 */
class CompanyCrudTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): Authenticatable
    {
        // Arrange: herbruikbare hulpfunctie voor admin-context in meerdere tests.
        $adminRole = Role::factory()->create(['name' => 'admin', 'label' => 'Administrator']);

        return User::factory()->createOne([
            'role_id'            => $adminRole->id,
            'email_verified_at'  => now(),
        ]);
    }

    public function test_admin_kan_bedrijf_aanmaken(): void
    {
        // Arrange: admin en geldige bedrijfsdata.
        $admin = $this->createAdmin();

        // Act: POST naar de store-route met geldige data.
        $response = $this->actingAs($admin)->post(route('companies.store'), [
            'name'           => 'Tech Bedrijf BV',
            'contact_person' => 'Jan de Vries',
            'email'          => 'contact@techbedrijf.nl',
            'phone'          => '0201234567',
            'city'           => 'Amsterdam',
            'industry'       => 'Software',
            'website'        => 'https://techbedrijf.nl',
            'status'         => 'active',
        ]);

        // Assert: redirect naar bedrijvenlijst en record aanwezig in database.
        $response->assertRedirect(route('companies.index'));
        $this->assertDatabaseHas('companies', [
            'name'  => 'Tech Bedrijf BV',
            'email' => 'contact@techbedrijf.nl',
        ]);
    }

    public function test_bedrijf_naam_moet_uniek_zijn(): void
    {
        // Arrange: bedrijf bestaat al in de database.
        $admin = $this->createAdmin();
        Company::factory()->create(['name' => 'Bestaand Bedrijf BV']);

        // Act: probeer een tweede bedrijf met dezelfde naam aan te maken.
        $response = $this->actingAs($admin)->post(route('companies.store'), [
            'name'           => 'Bestaand Bedrijf BV',
            'contact_person' => 'Piet Jansen',
            'email'          => 'nieuw@bedrijf.nl',
            'city'           => 'Rotterdam',
            'industry'       => 'Logistiek',
            'status'         => 'active',
        ]);

        // Assert: validatiefout op het naam-veld.
        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('companies', 1);
    }

    public function test_admin_kan_bedrijf_verwijderen(): void
    {
        // Arrange: admin en bestaand bedrijf.
        $admin   = $this->createAdmin();
        $company = Company::factory()->create();

        // Act: DELETE verzoek naar het destroy-eindpunt.
        $response = $this->actingAs($admin)->delete(route('companies.destroy', $company));

        // Assert: redirect en record verwijderd uit database.
        $response->assertRedirect(route('companies.index'));
        $this->assertDatabaseMissing('companies', ['id' => $company->id]);
    }
}