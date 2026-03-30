<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Cache unread count for 30 seconds
        $unreadCount = Cache::remember("user_{$user->id}_unread_notifications", 30, function () use ($user) {
            return $user->unreadNotifications()->count();
        });

        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();
            // Clear cache when notification is marked as read
            Cache::forget("user_{$user->id}_unread_notifications");
        }

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();

        // Clear cache when all notifications are marked as read
        Cache::forget("user_{$user->id}_unread_notifications");

        return response()->json(['success' => true]);
    }
}
