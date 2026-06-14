<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Game;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Game>
 */
class GameFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->unique()->words(3, true),
            'developer_id' => User::factory()->developer(),
            'category_id' => Category::factory(),
            'price' => fake()->numberBetween(5000, 100000),
            'description' => fake()->paragraphs(3, true),
            'trailer_url' => 'https://www.youtube.com/embed/'.fake()->regexify('[A-Za-z0-9_-]{11}'),
            'status' => fake()->randomElement(['pending', 'approved', 'approved', 'approved']),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }
}
