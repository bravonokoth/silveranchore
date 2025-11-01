<?php
// app/Mail/OrderDelivered.php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderDelivered extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $order;
    public $customerName;
    public $orderTotal;

    public function __construct(Order $order)
    {
        $this->order = $order->load('items.product', 'shippingAddress');
        $this->customerName = $order->shippingAddress->name;
        $this->orderTotal = number_format($order->total, 2);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Order Has Been Delivered - #'.$this->order->id,
            to: [$this->order->email],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-delivered',
            with: [
                'order' => $this->order,
                'customerName' => $this->customerName,
                'orderTotal' => $this->orderTotal,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}