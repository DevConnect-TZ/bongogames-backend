<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'game_id' => Game::factory()->approved(),
            'rating' => fake()->numberBetween(1, 5),
            'text' => fake()->paragraph(),
        ];
    }
}
