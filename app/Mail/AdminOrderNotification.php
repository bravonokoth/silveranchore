<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminOrderNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order->load(['shippingAddress', 'items.product']);
    }

    public function build()
    {
        return $this->subject("New Order #{$this->order->id} – Action Required")
                    ->markdown('emails.admin.order');
    }
}