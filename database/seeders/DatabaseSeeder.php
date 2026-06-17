<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Internship;
use App\Models\Review;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminRole = Role::query()->firstOrCreate(['name' => 'admin'], ['label' => 'Administrator']);
        $studentRole = Role::query()->firstOrCreate(['name' => 'student'], ['label' => 'Student']);
        $companyRole = Role::query()->firstOrCreate(['name' => 'company'], ['label' => 'Bedrijf']);

        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@stagems.local'],
            [
                'name' => 'Stage Admin',
                'password' => Hash::make('Password123!'),
                'email_verified_at' => now(),
                'role_id' => $adminRole->id,
            ],
        );

        $studentUser = User::query()->firstOrCreate(
            ['email' => 'student@stagems.local'],
            [
                'name' => 'Demo Student',
                'password' => Hash::make('Password123!'),
                'email_verified_at' => now(),
                'role_id' => $studentRole->id,
            ],
        );

        $companyUser = User::query()->firstOrCreate(
            ['email' => 'bedrijf@stagems.local'],
            [
                'name' => 'Demo Bedrijf',
                'password' => Hash::make('Password123!'),
                'email_verified_at' => now(),
                'role_id' => $companyRole->id,
            ],
        );

        $student = Student::query()->firstOrCreate(
            ['student_number' => 'S10001'],
            [
                'user_id' => $studentUser->id,
                'first_name' => 'Daan',
                'last_name' => 'Jansen',
                'email' => 'daan.jansen@student.local',
                'phone' => '0612345678',
                'program' => 'Software Development',
                'start_year' => 2024,
                'status' => 'active',
            ],
        );

        $company = Company::query()->firstOrCreate(
            ['name' => 'Bright Future BV'],
            [
                'user_id' => $companyUser->id,
                'contact_person' => 'Sanne de Vries',
                'email' => 'contact@brightfuture.local',
                'phone' => '0201234567',
                'city' => 'Amsterdam',
                'industry' => 'IT',
                'website' => 'https://brightfuture.example',
                'status' => 'active',
            ],
        );

        $internship = Internship::query()->firstOrCreate(
            ['title' => 'Backend Development Stage'],
            [
                'student_id' => $student->id,
                'company_id' => $company->id,
                'description' => 'Bouwen van API endpoints en dashboard functies.',
                'start_date' => now()->startOfMonth(),
                'end_date' => now()->addMonths(5)->endOfMonth(),
                'hours_per_week' => 32,
                'mentor_name' => 'Sanne de Vries',
                'status' => 'active',
            ],
        );

        Review::query()->firstOrCreate(
            [
                'internship_id' => $internship->id,
                'review_date' => now()->toDateString(),
            ],
            [
                'reviewer_user_id' => $admin->id,
                'score' => 8,
                'feedback' => 'Goede inzet en duidelijke voortgang in de sprinttaken.',
                'recommendation' => 'yes',
            ],
        );
    }
}
