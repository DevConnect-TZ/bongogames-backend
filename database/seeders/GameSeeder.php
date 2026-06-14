<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Game;
use App\Models\GameScreenshot;
use App\Models\GameVersion;
use App\Models\User;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    public function run(): void
    {
        $developers = User::where('role', 'developer')->get();
        $categories = Category::all();

        if ($developers->isEmpty() || $categories->isEmpty()) {
            return;
        }

        $games = [
            [
                'title' => 'Shadow Quest: Dark Realms',
                'category' => 'action-rpg',
                'price' => 70000,
                'description' => 'Embark on an epic journey through dark realms filled with monsters, magic, and mystery. Master powerful combat skills and uncover ancient secrets.',
                'trailer_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            ],
            [
                'title' => 'Pixel Jump Adventures',
                'category' => 'platformer',
                'price' => 30000,
                'description' => 'Jump, dash, and wall-climb through colorful pixel worlds. Collect coins, defeat enemies, and rescue your friends in this retro-style platformer.',
                'trailer_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            ],
            [
                'title' => 'Turbo Rally Championship',
                'category' => 'racing',
                'price' => 50000,
                'description' => 'Race through stunning landscapes in high-speed rally cars. Customize your vehicle, master drift techniques, and compete in global tournaments.',
                'trailer_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            ],
            [
                'title' => 'Mind Maze',
                'category' => 'puzzle',
                'price' => 20000,
                'description' => 'Challenge your brain with hundreds of mind-bending puzzles. From logic grids to spatial challenges, each level tests a different cognitive skill.',
                'trailer_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            ],
            [
                'title' => 'Wild Frontier Survival',
                'category' => 'survival',
                'price' => 60000,
                'description' => 'Stranded in the wilderness with nothing but your wits. Hunt, build shelter, craft tools, and survive against the elements and wild predators.',
                'trailer_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            ],
            [
                'title' => 'City Builder Tycoon',
                'category' => 'simulation',
                'price' => 45000,
                'description' => 'Design and manage your dream city. Balance budgets, keep citizens happy, and watch your metropolis grow from a small town to a bustling capital.',
                'trailer_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            ],
        ];

        foreach ($games as $i => $data) {
            $category = $categories->firstWhere('slug', $data['category']);
            $developer = $developers->random();

            $game = Game::create([
                'title' => $data['title'],
                'developer_id' => $developer->id,
                'category_id' => $category->id,
                'price' => $data['price'],
                'description' => $data['description'],
                'trailer_url' => $data['trailer_url'],
                'status' => 'approved',
            ]);

            GameScreenshot::create([
                'game_id' => $game->id,
                'path' => 'screenshots/placeholder.png',
                'order' => 0,
            ]);

            GameVersion::create([
                'game_id' => $game->id,
                'version' => '1.0.0',
                'changelog' => 'Initial release.',
                'download_path' => 'https://github.com/bongogames/demo/releases/download/v1.0.0/'.fake()->slug().'.apk',
            ]);
        }
    }
}
