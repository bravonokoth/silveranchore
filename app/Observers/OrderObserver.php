<?php
// app/Observers/OrderObserver.php

namespace App\Observers;

use App\Models\Order;
use App\Mail\OrderDelivered;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        // Check if status was changed to 'delivered'
        if ($order->isDirty('status') && $order->status === 'delivered') {
            Log::info('Order status changed to delivered', [
                'order_id' => $order->id,
                'email' => $order->email
            ]);

            try {
                // Make sure order has relationships loaded
                $order->load(['shippingAddress', 'items.product']);
                
                // Send delivery email
                Mail::to($order->email)->queue(new OrderDelivered($order));
                
                Log::info('Delivery email queued successfully', [
                    'order_id' => $order->id
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send delivery email', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}