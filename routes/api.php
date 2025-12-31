<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\CommentController;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('resend-otp', [AuthController::class, 'resendOtp']);
    Route::get('google', [AuthController::class, 'redirectToGoogle']);
    Route::post('google/callback', [AuthController::class, 'handleGoogleCallback']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
});

Route::get('articles', [ArticleController::class, 'index']);
Route::get('articles/popular', [ArticleController::class, 'popular']);
Route::get('articles/{article}', [ArticleController::class, 'show']);
Route::get('articles/{article}/comments', [CommentController::class, 'index']);
Route::get('videos', [VideoController::class, 'index']);
Route::post('videos/{id}/view', [VideoController::class, 'incrementViews']);

Route::middleware('auth:api')->group(function () {
    Route::post('subscribe', [SubscriptionController::class, 'subscribe']);

    // Article Routes for Authenticated Users
    Route::get('my-articles', [ArticleController::class, 'myArticles']);
    Route::post('articles', [ArticleController::class, 'store']);
    Route::put('articles/{article}', [ArticleController::class, 'update']);
    Route::delete('articles/{article}', [ArticleController::class, 'destroy']); // Logic in controller handles permissions
    Route::post('articles/{id}/restore', [ArticleController::class, 'restore']);
    Route::get('articles/{article}/logs', [ArticleController::class, 'logs']);
    Route::post('uploads/images', [UploadController::class, 'store']);

    // Comment Routes
    Route::post('articles/{article}/comments', [CommentController::class, 'store']);
    Route::put('comments/{comment}', [CommentController::class, 'update']);
    Route::delete('comments/{comment}', [CommentController::class, 'destroy']);

    // Notification Routes
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::put('notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
    Route::put('notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    
    // Sidebar Menu Route
    Route::get('sidebar-menu', function (\Illuminate\Http\Request $request) {
        $user = $request->user();
        $menu = [];
        
        // Common menu for all auth users
        $menu[] = [
            'label' => 'Artikel Saya',
            'to' => '/my-articles',
            'icon' => 'document'
        ];

        // Admin menu
        if ($user->is_admin) {
            $menu[] = [
                'label' => 'Verifikasi Artikel',
                'to' => '/admin/pending-articles',
                'icon' => 'verify'
            ];
            $menu[] = [
                'label' => 'Kelola User',
                'to' => '/admin/users',
                'icon' => 'users'
            ];
        }

        return response()->json($menu);
    });
});

// Admin Routes
Route::middleware('admin')->group(function () {
    Route::get('admin/pending-articles', [AdminController::class, 'pendingArticles']);
    Route::post('admin/articles/{article}/approve', [AdminController::class, 'approveArticle']);
    Route::post('admin/articles/{article}/reject', [AdminController::class, 'rejectArticle']);
    Route::get('admin/verification-stats', [AdminController::class, 'getVerificationStats']);
    Route::get('admin/users', [AdminController::class, 'users']);
    Route::delete('admin/users/{user}', [AdminController::class, 'deleteUser']);
    Route::put('admin/users/{user}/toggle-active', [AdminController::class, 'toggleActive']);
    Route::get('admin/users/{user}/backup', [AdminController::class, 'backupUser']);
    Route::get('admin/audit-logs', [AdminController::class, 'auditLogs']);
    Route::post('admin/videos', [VideoController::class, 'store']);
    Route::delete('admin/videos/{video}', [VideoController::class, 'destroy']);
});
