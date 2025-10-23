<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Unicodeveloper\Paystack\Facades\Paystack;

class PaymentController extends Controller
{
    /**
     * Initialize a Paystack payment.
     */
    public function initialize(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'amount' => 'required|numeric|min:0.5',
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::findOrFail($validated['order_id']);

        $paymentData = [
            'amount' => $validated['amount'] * 100, // Paystack uses kobo
            'reference' => Paystack::genTranxRef(), // Generate unique reference
            'email' => $validated['email'],
            'callback_url' => route('payment.callback'),
            'metadata' => [
                'order_id' => $order->id,
                'customer_email' => $validated['email'],
                'custom_fields' => [
                    [
                        'display_name' => 'Order ID',
                        'variable_name' => 'order_id',
                        'value' => $order->id
                    ]
                ]
            ],
        ];

        try {
            return Paystack::getAuthorizationUrl($paymentData)->redirectNow();
        } catch (\Exception $e) {
            Log::error('Paystack Init Error: ' . $e->getMessage());
            return back()->with('error', 'Unable to initialize payment. Please try again.');
        }
    }

    /**
     * Handle Paystack callback after payment.
     */
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
                $order->update([
                    'payment_status' => 'paid',
                    'payment_reference' => $paymentDetails['data']['reference'],
                    'paid_at' => now()
                ]);
                
                Log::info("Order {$order->id} marked as PAID.");
                
                return redirect()->route('orders.success', $order)
                    ->with('success', 'Payment successful!');
            }

            $order->update(['payment_status' => 'failed']);
            Log::warning("Order {$order->id} payment failed.");
            
            return redirect()->route('checkout.index')
                ->with('error', 'Payment failed. Please try again.');
                
        } catch (\Exception $e) {
            Log::error('PAYSTACK CALLBACK ERROR: ' . $e->getMessage());
            return redirect('/')->with('error', 'Payment verification failed.');
        }
    }

    /**
     * Webhook endpoint for Paystack server-to-server notifications
     */
    public function webhook(Request $request)
    {
        // Verify webhook signature
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
                    $order->update([
                        'payment_status' => 'paid',
                        'payment_reference' => $event['data']['reference'],
                        'paid_at' => now()
                    ]);
                    Log::info("Order {$order->id} updated via webhook.");
                }
            }
        }

        return response()->json(['status' => 'success'], 200);
    }
}