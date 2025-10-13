<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class NewUserRegisteredNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
        Log::info('NewUserRegisteredNotification created for user: ' . $user->email);
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        $data = [
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'email' => $this->user->email,
            'message' => 'New user ' . $this->user->name . ' (' . $this->user->email . ') has registered.',
            'created_at' => now()->toDateTimeString(),
        ];
        Log::info('NewUserRegisteredNotification toArray for notifiable: ' . $notifiable->id, $data);
        return $data;
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $data = [
            'id' => $this->id,
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'email' => $this->user->email,
            'message' => 'New user ' . $this->user->name . ' (' . $this->user->email . ') has registered.',
            'created_at' => now()->toDateTimeString(),
        ];
        Log::info('NewUserRegisteredNotification toBroadcast for notifiable: ' . $notifiable->id, $data);
        return new BroadcastMessage($data);
    }
}