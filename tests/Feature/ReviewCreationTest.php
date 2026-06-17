<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Internship;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Acceptatietests voor beoordelingen (reviews).
 *
 * Controleert dat een admin beoordelingen kan aanmaken met geldige data,
 * en dat score-validatie correct werkt.
 */
class ReviewCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_kan_beoordeling_aanmaken_voor_stage(): void
    {
        // Arrange: volledige context opzetten (admin, student, bedrijf, stage).
        $adminRole = Role::factory()->create(['name' => 'admin', 'label' => 'Administrator']);
        $admin     = User::factory()->createOne([
            'role_id'           => $adminRole->id,
            'email_verified_at' => now(),
        ]);

        $student    = Student::factory()->create(['student_number' => 'S10001', 'email' => 'S10001@student.local']);
        $company    = Company::factory()->create();
        $internship = Internship::factory()->create([
            'student_id' => $student->id,
            'company_id' => $company->id,
            'status'     => 'completed',
        ]);

        // Act: POST beoordeling voor de voltooide stage.
        $response = $this->actingAs($admin)->post(route('reviews.store'), [
            'internship_id' => $internship->id,
            'score'         => 8,
            'feedback'      => 'Uitstekende stage, student heeft veel geleerd en goede initiatieven genomen.',
            'review_date'   => now()->toDateString(),
            'recommendation'=> 'yes',
        ]);

        // Assert: redirect en beoordeling aanwezig in database met correcte koppeling.
        $response->assertRedirect(route('reviews.index'));
        $this->assertDatabaseHas('reviews', [
            'internship_id' => $internship->id,
            'score'         => 8,
            'recommendation'=> 'yes',
        ]);
    }

    public function test_score_buiten_bereik_geeft_validatiefout(): void
    {
        // Arrange: admin context voor geautoriseerde request.
        $adminRole = Role::factory()->create(['name' => 'admin', 'label' => 'Administrator']);
        $admin     = User::factory()->createOne([
            'role_id'           => $adminRole->id,
            'email_verified_at' => now(),
        ]);

        // Act: score van 11 is buiten het geldige bereik 1-10.
        $response = $this->actingAs($admin)->post(route('reviews.store'), [
            'internship_id' => 1,
            'score'         => 11,
            'feedback'      => 'Test feedback voor validatie.',
            'review_date'   => now()->toDateString(),
            'recommendation'=> 'yes',
        ]);

        // Assert: validatiefout op score-veld.
        $response->assertSessionHasErrors('score');
    }
}