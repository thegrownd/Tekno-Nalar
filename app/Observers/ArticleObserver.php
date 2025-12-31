<?php

namespace App\Observers;

use App\Mail\NewArticleNotification;
use App\Models\Article;
use App\Models\Subscriber;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ArticleObserver
{
    /**
     * Handle the Article "created" event.
     */
    public function created(Article $article): void
    {
        // If created directly as published (e.g. by admin)
        if ($article->status === 'published') {
            $this->sendNotification($article);
        }
    }

    /**
     * Handle the Article "updated" event.
     */
    public function updated(Article $article): void
    {
        // If status changed to published
        if ($article->isDirty('status') && $article->status === 'published') {
            $this->sendNotification($article);
        }
    }

    /**
     * Send notification to all subscribers
     */
    protected function sendNotification(Article $article)
    {
        Log::info("Starting newsletter distribution for article: {$article->title}");

        Subscriber::active()->chunk(50, function ($subscribers) use ($article) {
            foreach ($subscribers as $subscriber) {
                try {
                    Mail::to($subscriber->email)->queue(new NewArticleNotification($article, $subscriber));
                } catch (\Exception $e) {
                    Log::error("Failed to queue email for {$subscriber->email}: " . $e->getMessage());
                }
            }
        });

        Log::info("Newsletter distribution queued.");
    }

    /**
     * Handle the Article "deleted" event.
     */
    public function deleted(Article $article): void
    {
        //
    }

    /**
     * Handle the Article "restored" event.
     */
    public function restored(Article $article): void
    {
        //
    }

    /**
     * Handle the Article "force deleted" event.
     */
    public function forceDeleted(Article $article): void
    {
        //
    }
}
