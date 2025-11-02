<?php
// app/Console/Commands/TestEmailCommand.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\Order;
use App\Mail\OrderConfirmed;
use App\Mail\OrderDelivered;

class TestEmailCommand extends Command
{
    protected $signature = 'test:email {order_id} {type=confirmed}';
    protected $description = 'Test sending order emails';

    public function handle()
    {
        $orderId = $this->argument('order_id');
        $type = $this->argument('type');

        $order = Order::with(['shippingAddress', 'items.product'])->find($orderId);

        if (!$order) {
            $this->error("Order #{$orderId} not found!");
            return 1;
        }

        $this->info("Sending {$type} email to: {$order->email}");

        try {
            if ($type === 'confirmed') {
                Mail::to($order->email)->send(new OrderConfirmed($order));
            } elseif ($type === 'delivered') {
                Mail::to($order->email)->send(new OrderDelivered($order));
            }

            $this->info("Email sent successfully!");
            return 0;
        } catch (\Exception $e) {
            $this->error("Failed to send email: " . $e->getMessage());
            return 1;
        }
    }
}