<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
            'contact_person' => fake()->name(),
            'email' => fake()->unique()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'city' => fake()->city(),
            'industry' => fake()->randomElement(['IT', 'Zorg', 'Financieel', 'Techniek']),
            'website' => fake()->url(),
            'status' => fake()->randomElement(['active', 'inactive']),
        ];
    }
}
