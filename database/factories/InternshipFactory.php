<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Internship;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Internship>
 */
class InternshipFactory extends Factory
{
    protected $model = Internship::class;

    public function definition(): array
    {
        $start = fake()->dateTimeBetween('-2 months', '+2 months');

        return [
            'student_id' => Student::factory(),
            'company_id' => Company::factory(),
            'title' => fake()->randomElement(['Frontend Stage', 'Backend Stage', 'Data Stage']),
            'description' => fake()->paragraph(),
            'start_date' => $start,
            'end_date' => (clone $start)->modify('+5 months'),
            'hours_per_week' => fake()->numberBetween(24, 40),
            'mentor_name' => fake()->name(),
            'status' => fake()->randomElement(['planned', 'active', 'completed', 'cancelled']),
        ];
    }
}
