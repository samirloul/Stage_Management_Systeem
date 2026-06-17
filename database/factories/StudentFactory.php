<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'student_number' => 'S'.fake()->unique()->numberBetween(10000, 99999),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'program' => fake()->randomElement(['Software Development', 'ICT Beheer', 'Business IT']),
            'start_year' => (int) fake()->numberBetween(2020, 2026),
            'status' => fake()->randomElement(['active', 'inactive', 'graduated']),
        ];
    }
}
