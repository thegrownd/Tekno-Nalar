<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth('api')->user()->notifications()->latest()->limit(20)->get();
        return response()->json($notifications);
    }

    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id !== auth('api')->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $notification->update(['is_read' => true]);

        return response()->json(['message' => 'Marked as read']);
    }

    public function markAllAsRead()
    {
        auth('api')->user()->notifications()->where('is_read', false)->update(['is_read' => true]);

        return response()->json(['message' => 'All marked as read']);
    }
}
