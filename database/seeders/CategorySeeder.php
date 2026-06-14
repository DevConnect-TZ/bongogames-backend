<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Action RPG', 'slug' => 'action-rpg'],
            ['name' => 'Platformer', 'slug' => 'platformer'],
            ['name' => 'Racing', 'slug' => 'racing'],
            ['name' => 'Puzzle', 'slug' => 'puzzle'],
            ['name' => 'Survival', 'slug' => 'survival'],
            ['name' => 'Simulation', 'slug' => 'simulation'],
            ['name' => 'Strategy', 'slug' => 'strategy'],
            ['name' => 'Adventure', 'slug' => 'adventure'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
