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
            'amount' => $validated['amount'] * 100, // convert to kobo
            'reference' => 'order_' . $order->id,
            'email' => $validated['email'],
            'callback_url' => route('payment.callback'),
            'metadata' => [
                'order_id' => $order->id,
                'customer_email' => $validated['email']
            ],
        ];

        Log::info('=== PAYSTACK INITIALIZE ===', $paymentData);

        return Paystack::getAuthorizationUrl($paymentData)->redirectNow();
    }

    /**
     * Handle Paystack callback after payment.
     */
    public function callback()
    {
        try {
            $paymentDetails = Paystack::getPaymentData();
            Log::info('=== PAYSTACK CALLBACK ===', $paymentDetails);

            $reference = $paymentDetails['data']['reference'] ?? null;
            $orderId = $paymentDetails['data']['metadata']['order_id'] ?? null;

            if (!$reference || !$orderId) {
                throw new \Exception('Invalid payment callback payload');
            }

            $order = Order::findOrFail($orderId);

            if ($paymentDetails['data']['status'] === 'success') {
                $order->update(['status' => 'paid']);
                Log::info("Order {$order->id} marked as PAID.");
                return redirect()->route('order.success', ['order' => $order->id])
                                 ->with('success', 'Payment successful!');
            } else {
                $order->update(['status' => 'failed']);
                Log::warning("Order {$order->id} payment failed.");
                return redirect()->route('order.failed', ['order' => $order->id])
                                 ->with('error', 'Payment failed.');
            }
        } catch (\Exception $e) {
            Log::error('PAYSTACK CALLBACK ERROR: ' . $e->getMessage());
            return redirect('/')->with('error', 'Payment verification failed.');
        }
    }

    /**
     * Webhook endpoint (optional, for server-to-server verification)
     */
    public function webhook(Request $request)
    {
        $payload = $request->all();
        Log::info('=== PAYSTACK WEBHOOK ===', $payload);

        if (($payload['event'] ?? null) === 'charge.success') {
            $orderId = $payload['data']['metadata']['order_id'] ?? null;
            if ($orderId) {
                $order = Order::find($orderId);
                if ($order) {
                    $order->update(['status' => 'paid']);
                    Log::info("Order {$order->id} updated via webhook.");
                }
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
