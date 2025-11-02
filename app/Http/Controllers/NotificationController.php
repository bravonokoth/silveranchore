<?php
// app/Http/Controllers/NotificationController.php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'markAsRead']);
    }

    public function index()
    {
        $user = Auth::user();
        $sessionId = session()->getId();

        $userNotifications = $user?->notifications()->latest()->get() ?? collect();

        $guestNotifications = Notification::query()
            ->whereNull('notifiable_id')
            ->whereJsonContains('data->session_id', $sessionId)
            ->latest()
            ->get();

        $notifications = $userNotifications
            ->merge($guestNotifications)
            ->sortByDesc('created_at')
            ->values();

    $isAdmin = $user && $user->hasRole(['admin', 'super-admin']);

    return view('notifications.index', compact('notifications', 'isAdmin'));
    }

public function markAllRead()
    {
        $user = Auth::user();
        $sessionId = session()->getId();

        if ($user) {
            // Mark all user notifications as read
            $user->unreadNotifications()->update(['read_at' => now()]);
        }

        // Mark all guest notifications for this session as read
        Notification::query()
            ->whereNull('notifiable_id')
            ->whereNull('read_at')
            ->whereJsonContains('data->session_id', $sessionId)
            ->update(['read_at' => now()]);

        return back()->with('success', 'All notifications marked as read');
    }
public function destroy(Notification $notification)
    {
        $canDelete = 
            ($notification->notifiable_id && Auth::id() && $notification->notifiable_id == Auth::id()) ||
            (!$notification->notifiable_id && isset($notification->data['session_id']) && $notification->data['session_id'] === session()->getId());

        if (!$canDelete) {
            abort(403, 'Unauthorized to delete this notification');
        }

        $notification->delete();

        return back()->with('success', 'Notification deleted');
    }

}