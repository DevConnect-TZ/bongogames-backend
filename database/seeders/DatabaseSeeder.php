<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Alice',
            'username' => 'alice',
            'email' => 'alice@example.com',
            'phone' => '255712345678',
            'password' => 'password',
            'role' => 'user',
        ]);

        User::create([
            'name' => 'Bob',
            'username' => 'bob',
            'email' => 'bob@example.com',
            'phone' => '255723456789',
            'password' => 'password',
            'role' => 'user',
        ]);

        User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'phone' => '255700000000',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $developers = [
            ['name' => 'Nebula Studios', 'username' => 'nebula', 'email' => 'nebula@example.com', 'phone' => '255711111111', 'bio' => 'Award-winning indie game studio specializing in immersive RPG experiences.'],
            ['name' => 'PixelForge', 'username' => 'pixelforge', 'email' => 'pixelforge@example.com', 'phone' => '255722222222', 'bio' => 'Retro-inspired game developers crafting pixel-perfect platformers and adventures.'],
            ['name' => 'TurboSoft', 'username' => 'turbosoft', 'email' => 'turbosoft@example.com', 'phone' => '255733333333', 'bio' => 'High-octane racing game specialists pushing the limits of mobile gaming.'],
            ['name' => 'MindLab', 'username' => 'mindlab', 'email' => 'mindlab@example.com', 'phone' => '255744444444', 'bio' => 'Brain-training puzzle creators designing games that challenge and entertain.'],
            ['name' => 'Undead Interactive', 'username' => 'undead', 'email' => 'undead@example.com', 'phone' => '255755555555', 'bio' => 'Survival game experts building intense wilderness experiences.'],
            ['name' => 'Green Valley Games', 'username' => 'greenvalley', 'email' => 'greenvalley@example.com', 'phone' => '255766666666', 'bio' => 'Simulation game developers creating rich, detailed virtual worlds.'],
        ];

        foreach ($developers as $dev) {
            User::create(array_merge($dev, ['password' => 'password', 'role' => 'developer']));
        }

        $this->call([
            CategorySeeder::class,
            GameSeeder::class,
            ReviewSeeder::class,
            PurchaseSeeder::class,
            WishlistSeeder::class,
        ]);
    }
}
