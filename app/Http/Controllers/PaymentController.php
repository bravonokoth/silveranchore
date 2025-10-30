<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Notification;
use App\Events\NotificationSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Unicodeveloper\Paystack\Facades\Paystack;

class PaymentController extends Controller
{
    public function initialize(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'amount' => 'required|numeric|min:0.5',
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::findOrFail($validated['order_id']);

        $paymentData = [
            'amount' => $validated['amount'] * 100,
            'reference' => Paystack::genTranxRef(),
            'email' => $validated['email'],
            'callback_url' => route('payment.callback'),
            'metadata' => [
                'order_id' => $order->id,
                'customer_email' => $validated['email'],
                'custom_fields' => [
                    [
                        'display_name' => 'Order ID',
                        'variable_name' => 'order_id',
                        'value' => $order->id,
                    ],
                ],
            ],
        ];

        try {
            $order->update(['payment_reference' => $paymentData['reference'], 'payment_method' => 'paystack']);
            Log::info('✅ Paystack initialized for Order #' . $order->id, $paymentData);
            return Paystack::getAuthorizationUrl($paymentData)->redirectNow();
        } catch (\Exception $e) {
            Log::error('Paystack Init Error: ' . $e->getMessage(), ['order_id' => $order->id]);
            return back()->with('error', 'Unable to initialize payment. Please try again.');
        }
    }

    public function callback()
    {
        try {
            $paymentDetails = Paystack::getPaymentData();
            Log::info('=== PAYSTACK CALLBACK ===', $paymentDetails);

            $orderId = $paymentDetails['data']['metadata']['order_id'] ?? null;
            if (!$orderId) {
                throw new \Exception('Order ID not found in payment metadata');
            }

            $order = Order::findOrFail($orderId);

            if ($paymentDetails['data']['status'] === 'success') {
                $updated = $order->update([
                    'payment_status' => 'paid',
                    'status' => 'processing',
                    'payment_reference' => $paymentDetails['data']['reference'],
                    'payment_method' => 'paystack',
                    'paid_at' => now(),
                ]);

                if (!$updated) {
                    throw new \Exception('Failed to update order payment status');
                }

                $notificationEmail = $order->email ?? ($order->shippingAddress ? $order->shippingAddress->email : $paymentDetails['data']['customer']['email']);

                Notification::create([
                    'user_id' => $order->user_id,
                    'session_id' => $order->session_id,
                    'email' => $notificationEmail,
                    'message' => "Payment for order #{$order->id} completed successfully.",
                    'is_read' => false,
                ]);

                event(new NotificationSent(
                    "Payment for order #{$order->id} completed successfully.",
                    $notificationEmail,
                    $order->user_id,
                    $order->session_id
                ));

                Log::info("✅ Order #{$order->id} marked as PAID.", [
                    'payment_reference' => $paymentDetails['data']['reference'],
                ]);

                return redirect()->route('orders.success', $order)
                    ->with('success', 'Payment successful!');
            }

            $updated = $order->update([
                'payment_status' => 'failed',
                'status' => 'cancelled',
            ]);
            Log::warning("❌ Order #{$order->id} payment failed.", [
                'reason' => $paymentDetails['data']['gateway_response'] ?? 'Unknown',
            ]);

            return redirect()->route('checkout.index')
                ->with('error', 'Payment failed. Please try again.');
        } catch (\Exception $e) {
            Log::error('💥 PAYSTACK CALLBACK ERROR: ' . $e->getMessage(), [
                'order_id' => $orderId ?? 'unknown',
            ]);
            return redirect()->route('home')
                ->with('error', 'Payment verification failed. Please contact support.');
        }
    }

    public function webhook(Request $request)
    {
        $signature = $request->header('x-paystack-signature');
        $payload = $request->getContent();

        if ($signature !== hash_hmac('sha512', $payload, config('paystack.secretKey'))) {
            Log::warning('Invalid Paystack webhook signature');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $event = json_decode($payload, true);
        Log::info('=== PAYSTACK WEBHOOK ===', $event);

        if ($event['event'] === 'charge.success') {
            $orderId = $event['data']['metadata']['order_id'] ?? null;
            if ($orderId) {
                $order = Order::find($orderId);
                if ($order && $order->payment_status !== 'paid') {
                    $updated = $order->update([
                        'payment_status' => 'paid',
                        'payment_reference' => $event['data']['reference'],
                        'payment_method' => 'paystack',
                        'paid_at' => now(),
                    ]);

                    if ($updated) {
                        $notificationEmail = $order->email ?? ($order->shippingAddress ? $order->shippingAddress->email : $event['data']['customer']['email']);

                        Notification::create([
                            'user_id' => $order->user_id,
                            'session_id' => $order->session_id,
                            'email' => $notificationEmail,
                            'message' => "Payment for order #{$order->id} completed successfully via webhook.",
                            'is_read' => false,
                        ]);

                        event(new NotificationSent(
                            "Payment for order #{$order->id} completed successfully via webhook.",
                            $notificationEmail,
                            $order->user_id,
                            $order->session_id
                        ));

                        Log::info("Order #{$order->id} updated via webhook.", [
                            'payment_reference' => $event['data']['reference'],
                        ]);
                    }
                }
            }
        }

        return response()->json(['status' => 'success'], 200);
    }
}