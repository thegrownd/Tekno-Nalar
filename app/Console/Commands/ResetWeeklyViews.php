<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Article;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ResetWeeklyViews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articles:reset-weekly-views';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset weekly views counter for all articles and mark top 10 as historically popular';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting weekly views reset...');

        try {
            DB::transaction(function () {
                // 1. Identify top 10 articles of the week
                $topArticles = Article::where('status', 'published')
                    ->where('weekly_views_count', '>', 0) // Only consider active articles
                    ->orderByDesc('weekly_views_count')
                    ->take(10)
                    ->pluck('id');

                if ($topArticles->isNotEmpty()) {
                    // 2. Mark them as historically popular so they won't appear in future top lists
                    Article::whereIn('id', $topArticles)
                        ->update(['is_historically_popular' => true]);
                    
                    $this->info('Marked ' . $topArticles->count() . ' articles as historically popular.');
                }

                // 3. Reset weekly views for ALL articles
                DB::table('articles')->update(['weekly_views_count' => 0]);
            });
            
            // Clear cache
            Cache::forget('articles.popular');
            
            $this->info('Weekly views reset successfully.');
            Log::info('Weekly views reset successfully via scheduled command.');
        } catch (\Exception $e) {
            $this->error('Failed to reset weekly views: ' . $e->getMessage());
            Log::error('Failed to reset weekly views: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
