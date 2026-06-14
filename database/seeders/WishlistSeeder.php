<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Database\Seeder;

class WishlistSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'user')->get();
        $games = Game::where('status', 'approved')->get();

        if ($users->isEmpty() || $games->isEmpty()) {
            return;
        }

        foreach ($users as $user) {
            $wishlistGames = $games->random(rand(1, min(2, $games->count())));

            foreach ($wishlistGames as $game) {
                if (! Wishlist::where('user_id', $user->id)->where('game_id', $game->id)->exists()) {
                    Wishlist::create([
                        'user_id' => $user->id,
                        'game_id' => $game->id,
                    ]);
                }
            }
        }
    }
}
