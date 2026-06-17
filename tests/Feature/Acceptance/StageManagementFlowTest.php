<?php

namespace Tests\Feature\Acceptance;

use App\Models\Company;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StageManagementFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_complete_core_flow(): void
    {
        $adminRole = Role::factory()->create(['name' => 'admin', 'label' => 'Administrator']);
        $admin = User::factory()->createOne([
            'role_id' => $adminRole->id,
            'email_verified_at' => now(),
        ]);
        $this->assertInstanceOf(User::class, $admin);
        /** @var Authenticatable $authAdmin */
        $authAdmin = $admin;

        $student = Student::factory()->create([
            'status' => 'active',
            'student_number' => 'S77777',
            'email' => 'flow.student@test.local',
        ]);

        $company = Company::factory()->create([
            'name' => 'Flow Company BV',
            'email' => 'flow.company@test.local',
        ]);

        $internshipResponse = $this->actingAs($authAdmin)->post(route('internships.store'), [
            'student_id' => $student->id,
            'company_id' => $company->id,
            'title' => 'Flow Stage',
            'description' => 'Complete flow test',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addMonths(5)->toDateString(),
            'hours_per_week' => 32,
            'mentor_name' => 'Mentor Test',
            'status' => 'planned',
        ]);

        $internshipResponse->assertRedirect(route('internships.index'));
        $this->assertDatabaseHas('internships', ['title' => 'Flow Stage']);

        $internshipId = (int) DB::table('internships')->where('title', 'Flow Stage')->value('id');

        $reviewResponse = $this->actingAs($authAdmin)->post(route('reviews.store'), [
            'internship_id' => $internshipId,
            'score' => 9,
            'feedback' => 'Sterke bijdrage aan het team.',
            'review_date' => now()->toDateString(),
            'recommendation' => 'yes',
        ]);

        $reviewResponse->assertRedirect(route('reviews.index'));

        $this->assertDatabaseHas('reviews', [
            'internship_id' => $internshipId,
            'score' => 9,
        ]);
    }

    public function test_active_internship_rejects_fully_past_period(): void
    {
        $adminRole = Role::factory()->create(['name' => 'admin', 'label' => 'Administrator']);
        $admin = User::factory()->createOne([
            'role_id' => $adminRole->id,
            'email_verified_at' => now(),
        ]);
        /** @var Authenticatable $authAdmin */
        $authAdmin = $admin;

        $student = Student::factory()->create([
            'status' => 'active',
            'student_number' => 'S88888',
            'email' => 'invalid.period@student.local',
        ]);

        $company = Company::factory()->create([
            'name' => 'Validation Company BV',
            'email' => 'validation.company@test.local',
        ]);

        $response = $this->actingAs($authAdmin)
            ->from(route('internships.create'))
            ->post(route('internships.store'), [
                'student_id' => $student->id,
                'company_id' => $company->id,
                'title' => 'Oude actieve stage',
                'description' => 'Deze stageperiode is volledig verlopen.',
                'start_date' => now()->subMonths(2)->toDateString(),
                'end_date' => now()->subMonth()->toDateString(),
                'hours_per_week' => 32,
                'mentor_name' => 'Mentor Test',
                'status' => 'active',
            ]);

        $response->assertRedirect(route('internships.create'));
        $response->assertSessionHasErrors(['status']);

        $this->assertDatabaseMissing('internships', [
            'title' => 'Oude actieve stage',
        ]);
    }
}
