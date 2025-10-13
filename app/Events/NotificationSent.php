<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class NotificationSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public $message;
    public $email;
    public $userId;

    public function __construct($message, $email = null, $userId = null)
    {
        $this->message = $message;
        $this->email = $email;
        $this->userId = $userId;
    }

    public function broadcastOn()
    {
        // Send to private admin channel
        return new PrivateChannel('admin.notifications');
    }

    public function broadcastAs()
    {
        return 'notification.sent';
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message,
            'email'   => $this->email,
            'userId'  => $this->userId,
            'time'    => now()->toDateTimeString(),
        ];
    }
}
