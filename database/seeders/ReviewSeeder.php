<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'user')->get();
        $games = Game::where('status', 'approved')->get();

        if ($users->isEmpty() || $games->isEmpty()) {
            return;
        }

        $reviews = [
            ['text' => 'Absolutely amazing game! The graphics are stunning and the gameplay is smooth.', 'rating' => 5],
            ['text' => 'Great game overall. A few minor bugs but nothing game-breaking.', 'rating' => 4],
            ['text' => 'Decent game. Could use more content and better controls.', 'rating' => 3],
            ['text' => 'Not what I expected. The trailer made it look better than it is.', 'rating' => 2],
            ['text' => 'Best game I have played this year! Highly recommend to everyone.', 'rating' => 5],
            ['text' => 'Good value for money. Kept me entertained for hours.', 'rating' => 4],
            ['text' => 'Fun gameplay but the story is a bit weak.', 'rating' => 3],
            ['text' => 'Addictive and well-designed. The developers really care about quality.', 'rating' => 5],
            ['text' => 'Solid game with great mechanics. Looking forward to updates.', 'rating' => 4],
            ['text' => 'Average experience. Nothing special but not bad either.', 'rating' => 3],
        ];

        foreach ($reviews as $data) {
            $user = $users->random();
            $game = $games->random();

            if (! Review::where('user_id', $user->id)->where('game_id', $game->id)->exists()) {
                Review::create([
                    'user_id' => $user->id,
                    'game_id' => $game->id,
                    'rating' => $data['rating'],
                    'text' => $data['text'],
                ]);
            }
        }
    }
}
