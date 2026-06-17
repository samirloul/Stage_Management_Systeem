<?php

namespace Database\Factories;

use App\Models\Internship;
use App\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        return [
            'internship_id' => Internship::factory(),
            'score' => fake()->numberBetween(5, 10),
            'feedback' => fake()->paragraph(),
            'review_date' => fake()->dateTimeBetween('-6 months', 'now'),
            'recommendation' => fake()->randomElement(['yes', 'no', 'maybe']),
        ];
    }
}
