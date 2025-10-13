<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\Events\BroadcastNotificationCreated;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        // Correct polymorphic relationship query
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->get();

        Log::info("Notifications fetched for user {$user->id}: {$notifications->count()} found");

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(Notification $notification)
    {
        $this->authorize('update', $notification);

        $notification->update(['read_at' => now()]);

        Log::info("Notification {$notification->id} marked as read by user {$notification->notifiable_id}");

        // Fire broadcast event properly (no user_id here)
        event(new BroadcastNotificationCreated($notification));

        return redirect()
            ->route('notifications.index')
            ->with('success', 'Notification marked as read');
    }
}
