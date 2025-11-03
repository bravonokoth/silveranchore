<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'markAsRead', 'markAllRead']);
    }

    public function index()
    {
        $user = Auth::user();
        $sessionId = session()->getId();

        $notifications = collect();

        if ($user) {
            $notifications = $user->notifications()->latest()->get();
        }

        // Guest notifications (stored with session_id in data)
        $guestNotifications = DatabaseNotification::query()
            ->whereNull('notifiable_type')
            ->whereNull('notifiable_id')
            ->whereJsonContains('data->session_id', $sessionId)
            ->latest()
            ->get();

        $notifications = $notifications->merge($guestNotifications)
            ->sortByDesc('created_at')
            ->values();

        $isAdmin = $user?->is_admin ?? false;

        return view('notifications.index', compact('notifications', 'isAdmin'));
    }

    public function markAllRead()
    {
        $user = Auth::user();
        $sessionId = session()->getId();

        if ($user) {
            $user->unreadNotifications->markAsRead();
        }

        DatabaseNotification::query()
            ->whereNull('notifiable_type')
            ->whereNull('notifiable_id')
            ->whereNull('read_at')
            ->whereJsonContains('data->session_id', $sessionId)
            ->update(['read_at' => now()]);

        return back()->with('success', 'All notifications marked as read');
    }

    public function markAsRead(DatabaseNotification $notification)
    {
        $this->authorizeNotification($notification);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function destroy(DatabaseNotification $notification)
    {
        $this->authorizeNotification($notification);
        $notification->delete();

        return back()->with('success', 'Notification deleted');
    }

    protected function authorizeNotification($notification)
    {
        $user = Auth::user();
        $sessionId = session()->getId();

        $canAccess = 
            ($notification->notifiable_id && $user && $notification->notifiable_id == $user->id) ||
            (!$notification->notifiable_id && 
             isset($notification->data['session_id']) && 
             $notification->data['session_id'] === $sessionId);

        if (!$canAccess) abort(403);
    }
}