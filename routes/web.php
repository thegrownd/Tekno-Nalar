<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StorageController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\SubscriptionController;

// Subscription
Route::get('/unsubscribe', [SubscriptionController::class, 'unsubscribe'])->name('unsubscribe');

// Sitemap & Robots
Route::get('/sitemap.xml', [SitemapController::class, 'index']);
Route::get('/robots.txt', function () {
    $content = "User-agent: *\nDisallow: /admin/\nDisallow: /api/\nAllow: /\nSitemap: " . url('/sitemap.xml');
    return response($content, 200)->header('Content-Type', 'text/plain');
});

// Serve storage files (must be before catch-all route)
Route::get('/storage/{path}', [StorageController::class, 'serve'])
    ->where('path', '.*')
    ->name('storage.serve');

Route::view('/{any}', 'app')->where('any', '.*');
