<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * 
     * Note: ArticleSeeder now includes duplication prevention.
     * It will only seed if no articles exist in the database.
     * This prevents articles from reappearing after deletion when server restarts.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ArticleSeeder::class,
        ]);
    }
}
