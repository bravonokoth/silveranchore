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
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        Log::info('Notifications fetched for user ' . $user->id . ': ' . $notifications->count());

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(Notification $notification)
    {
        $this->authorize('update', $notification);

        $notification->update(['read_at' => now()]);
        Log::info('Notification marked as read: ' . $notification->id);

        event(new BroadcastNotificationCreated(
            $notification->id,
            'App\\Models\\User',
            $notification->user_id,
            ['read_at' => $notification->read_at->toDateTimeString()]
        ));

        return redirect()->route('notifications.index')->with('success', 'Notification marked as read');
    }
}