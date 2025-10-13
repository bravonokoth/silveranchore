<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewUserRegisteredNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast', 'mail']; // Database, Reverb, email (optional)
    }

    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'email' => $this->user->email,
            'message' => 'New user ' . $this->user->name . ' (' . $this->user->email . ') has registered.',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => $this->id,
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'email' => $this->user->email,
            'message' => 'New user ' . $this->user->name . ' (' . $this->user->email . ') has registered.',
            'created_at' => now()->toDateTimeString(),
        ]);
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('New User Registration')
                    ->line('A new user has registered.')
                    ->line('Name: ' . $this->user->name)
                    ->line('Email: ' . $this->user->email)
                    ->action('View User', route('admin.users.show', $this->user))
                    ->line('Thank you for managing SilverAnchor!');
    }
}