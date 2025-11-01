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

    public function markAsRead(Notification $notification)
    {
        $canRead = 
            ($notification->notifiable_id && Auth::id() && $notification->notifiable_id == Auth::id()) ||
            ($notification->is_guest && $notification->session_id === session()->getId());

        if (!$canRead) abort(403);

        $notification->update(['read_at' => now()]);

        return back()->with('success', 'Marked as read');
    }
}