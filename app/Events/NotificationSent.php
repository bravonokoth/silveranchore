
<?php



namespace App\Events;

use Illuminate\Broadcasting\Channel;
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
        return $this->userId
            ? new PrivateChannel('user.' . $this->userId)
            : new Channel('guest.' . $this->email);
    }

    public function broadcastAs()
    {
        return 'notification.sent';
    }
}