<?php

namespace Tests\Unit;

use App\Models\Role;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_students(): void
    {
        $adminRole = Role::factory()->create(['name' => 'admin', 'label' => 'Administrator']);
        $admin = User::factory()->createOne(['role_id' => $adminRole->id]);

        $service = new AuthService();

        $this->assertTrue($service->canManageStudents($admin));
    }
}
