<?php
// app/Notifications/OrderStatusUpdated.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(
        protected $order,
        protected $message
    ) {}

    public function via($notifiable) { return ['database']; }

    public function toDatabase($notifiable)
    {
        return [
            'message' => $this->message,
            'email' => $this->order->email,
            'session_id' => $this->order->session_id,
            'order_id' => $this->order->id,
            'status' => $this->order->status,
        ];
    }
}