<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Database\Seeder;

class PurchaseSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'user')->get();
        $games = Game::where('status', 'approved')->get();

        if ($users->isEmpty() || $games->isEmpty()) {
            return;
        }

        foreach ($users as $user) {
            $purchasedGames = $games->random(rand(1, min(3, $games->count())));

            foreach ($purchasedGames as $game) {
                if (! Purchase::where('user_id', $user->id)->where('game_id', $game->id)->exists()) {
                    Purchase::create([
                        'user_id' => $user->id,
                        'game_id' => $game->id,
                        'price_paid' => $game->price,
                    ]);
                }
            }
        }
    }
}
