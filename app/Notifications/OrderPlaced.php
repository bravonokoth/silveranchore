<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Order;
use App\Models\User;

class OrderPlaced extends Notification
{
    use Queueable;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order->load(['shippingAddress', 'items.product']);
    }

    /** --------------------------------------------------------------
     *  DATABASE CHANNEL – this is what your inbox reads from
     *  -------------------------------------------------------------- */
    public function via($notifiable)
    {
        return ['database'];          // only stored in `notifications` table
    }

    /** --------------------------------------------------------------
     *  Build the array that will be json_encoded into `data` column
     *  -------------------------------------------------------------- */
    public function toDatabase($notifiable)
    {
        $shipping = $this->order->shippingAddress;

        // Build a short product list for the preview
        $products = $this->order->items->map(function ($i) {
            return "{$i->product->name} × {$i->quantity}";
        })->implode(', ');

        $message = "New order #{$this->order->id} from **{$shipping->name}**.\n"
                 . "Total: KSh " . number_format($this->order->total, 2) . "\n"
                 . "Items: {$products}";

        return [
            'order_id'    => $this->order->id,
            'status'      => $this->order->status,          // pending / paid …
            'email'       => $shipping->email,
            'session_id'  => $this->order->session_id,
            'message'     => $message,
            // URL that opens the admin order view (adjust route name if needed)
            'url'         => route('admin.orders.show', $this->order->id),
            // Extra data you may want in the detail pane
            'customer'    => [
                'name'   => $shipping->name,
                'phone'  => $shipping->phone_number,
                'address'=> $shipping->line1
                           . ($shipping->line2 ? ', '.$shipping->line2 : '')
                           . ', '.$shipping->city
                           . ', '.$shipping->country,
            ],
            'products'    => $this->order->items->map(function ($i) {
                return [
                    'name'     => $i->product->name,
                    'quantity' => $i->quantity,
                    'price'    => $i->product->price,
                ];
            })->toArray(),
        ];
    }

    /** --------------------------------------------------------------
     *  (Optional) Mail channel – to email an email to admin
     *  -------------------------------------------------------------- */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("New Order #{$this->order->id}")
            ->greeting("Hello {$notifiable->name},")
            ->line("A new order has been placed.")
            ->action('View Order', route('admin.orders.show', $this->order->id))
            ->line('Thank you!');
    }
}