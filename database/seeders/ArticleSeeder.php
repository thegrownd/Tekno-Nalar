<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        // Check if articles already exist to prevent duplication
        $existingArticlesCount = Article::count();
        
        if ($existingArticlesCount > 0) {
            $this->command->info("Articles already exist ({$existingArticlesCount} found). Skipping seeding to prevent duplication.");
            return;
        }

        // Check if admin user already exists
        $user = User::where('email', 'admin@gmail.com')->first();

        if (!$user) {
            $user = User::factory()->create([
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('Nagasaya1'),
                'is_admin' => true,
            ]);
            $this->command->info('Admin user created.');
        } else {
            if (! $user->is_admin) {
                $user->forceFill(['is_admin' => true])->save();
                $this->command->info('Admin user updated to super admin.');
            }
            $this->command->info('Admin user already exists.');
        }

        $this->command->info('Creating 10 sample articles...');

        DB::transaction(function () use ($user) {
            Article::factory()->count(10)->create([
                'user_id' => $user->id,
            ]);
        });

        $this->command->info('Articles seeded successfully!');
    }
}
