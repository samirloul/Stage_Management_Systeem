<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_student(): void
    {
        // Arrange: maak adminrol en admingebruiker voor geautoriseerde request.
        $adminRole = Role::factory()->create(['name' => 'admin', 'label' => 'Administrator']);
        $admin = User::factory()->createOne([
            'role_id' => $adminRole->id,
            'email_verified_at' => now(),
        ]);
        $this->assertInstanceOf(User::class, $admin);
        /** @var Authenticatable $authAdmin */
        $authAdmin = $admin;

        // Act: verstuur create-request zoals een admin-gebruiker.
        $response = $this->actingAs($authAdmin)->post(route('students.store'), [
            'first_name' => 'Fatima',
            'last_name' => 'Bakker',
            'phone' => '0600000000',
            'program' => 'Software Development',
            'start_year' => 2025,
            'status' => 'active',
        ]);

        // Assert: redirect en database bevatten nieuwe student met automatisch nummer/e-mail.
        $response->assertRedirect(route('students.index'));

        $this->assertDatabaseHas('students', [
            'student_number' => 'S10001',
            'email' => 'S10001@student.local',
            'first_name' => 'Fatima',
        ]);
    }

    public function test_deleted_student_number_is_reused_when_creating_new_student(): void
    {
        // Arrange: maak admin en basispayload.
        $adminRole = Role::factory()->create(['name' => 'admin', 'label' => 'Administrator']);
        $admin = User::factory()->createOne([
            'role_id' => $adminRole->id,
            'email_verified_at' => now(),
        ]);
        /** @var Authenticatable $authAdmin */
        $authAdmin = $admin;

        $basePayload = [
            'phone' => '0600000000',
            'program' => 'Software Development',
            'start_year' => 2025,
            'status' => 'active',
        ];

        $this->actingAs($authAdmin)->post(route('students.store'), array_merge($basePayload, [
            'first_name' => 'Anna',
            'last_name' => 'Student',
        ]));

        $this->actingAs($authAdmin)->post(route('students.store'), array_merge($basePayload, [
            'first_name' => 'Bram',
            'last_name' => 'Student',
        ]));

        $this->assertDatabaseHas('students', ['student_number' => 'S10001']);
        $this->assertDatabaseHas('students', ['student_number' => 'S10002']);

        $studentToDelete = Student::query()->where('student_number', 'S10001')->firstOrFail();
        $this->actingAs($authAdmin)->delete(route('students.destroy', $studentToDelete));

        $this->actingAs($authAdmin)->post(route('students.store'), array_merge($basePayload, [
            'first_name' => 'Celine',
            'last_name' => 'Student',
        ]));

        $this->assertDatabaseHas('students', [
            'student_number' => 'S10001',
            'email' => 'S10001@student.local',
            'first_name' => 'Celine',
        ]);
    }

    public function test_program_rejects_numbers_with_clear_validation_error(): void
    {
        // Arrange: admincontext om student aanmaakroute te mogen gebruiken.
        $adminRole = Role::factory()->create(['name' => 'admin', 'label' => 'Administrator']);
        $admin = User::factory()->createOne([
            'role_id' => $adminRole->id,
            'email_verified_at' => now(),
        ]);
        /** @var Authenticatable $authAdmin */
        $authAdmin = $admin;

        $response = $this->actingAs($authAdmin)
            ->from(route('students.create'))
            ->post(route('students.store'), [
                'first_name' => 'Fatima',
                'last_name' => 'Bakker',
                'phone' => '0600000000',
                'program' => 'ICT 2026',
                'start_year' => 2025,
                'status' => 'active',
            ]);

        $response->assertRedirect(route('students.create'));
        $response->assertSessionHasErrors(['program']);

        $this->assertDatabaseMissing('students', [
            'first_name' => 'Fatima',
            'last_name' => 'Bakker',
        ]);
    }
}
