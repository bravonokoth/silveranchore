<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Order;

class OrderPlaced extends Notification
{
    use Queueable;

    public function __construct(protected Order $order)
    {
        // Ensure relationships are loaded
        $this->order = $order->load(['shippingAddress', 'items.product']);
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        $s = $this->order->shippingAddress;
        $products = $this->order->items->map(fn($i) => "{$i->product->name} × {$i->quantity}")->implode(', ');

        return [
            'order_id'    => $this->order->id,
            'status'      => $this->order->status,
            'email'       => $s->email,
            'session_id'  => $this->order->session_id,
            'message'     => "New order #{$this->order->id} from **{$s->name}** – KSh " . number_format($this->order->total, 2) . " – {$products}",
            'url'         => route('admin.orders.show', $this->order->id),
            'customer'    => [
                'name'    => $s->name,
                'phone'   => $s->phone_number,
                'address' => $s->line1 . ($s->line2 ? ', ' . $s->line2 : '') . ', ' . $s->city . ', ' . $s->country,
            ],
            'products'    => $this->order->items->map(fn($i) => [
                'name'     => $i->product->name,
                'quantity' => $i->quantity,
                'price'    => $i->price,
            ])->toArray(),
        ];
    }

    public function toMail($notifiable)
    {
        $customerName = $this->order->shippingAddress->name;
        $orderTotal = number_format($this->order->total, 2);

        return (new MailMessage)
            ->subject("🔔 New Order #{$this->order->id} - " . config('app.name'))
            ->view('emails.admin.order', [
                'order' => $this->order,
                'customerName' => $customerName,
                'orderTotal' => $orderTotal,
            ]);
    }
}