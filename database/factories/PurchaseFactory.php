<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Purchase>
 */
class PurchaseFactory extends Factory
{
    public function definition(): array
    {
        $game = Game::factory()->approved()->create();

        return [
            'user_id' => User::factory(),
            'game_id' => $game->id,
            'price_paid' => $game->price,
        ];
    }
}
