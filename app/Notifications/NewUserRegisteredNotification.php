<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Broadcasting\PrivateChannel;

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
        return [
            'user_id'    => $this->user->id,
            'user_name'  => $this->user->name,
            'email'      => $this->user->email,
            'message'    => 'New user ' . $this->user->name . ' (' . $this->user->email . ') has registered.',
            'created_at' => now()->toDateTimeString(),
        ];
    }

    public function toDatabase(object $notifiable): array
    {
        $data = $this->toArray($notifiable);
        Log::info('NewUserRegisteredNotification stored for admin: ' . $notifiable->id, $data);
        return $data;
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $data = $this->toArray($notifiable);
        Log::info('NewUserRegisteredNotification broadcasted to admin channel.', $data);
        return new BroadcastMessage($data);
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('admin.notifications')
        ];
    }
}
