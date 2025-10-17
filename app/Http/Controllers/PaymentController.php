<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function initialize(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'amount' => 'required|numeric|min:0',
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::findOrFail($validated['order_id']);
        
        // Initialize Paystack
        $response = Http::withToken(env('PAYSTACK_SECRET_KEY'))
            ->post('https://api.paystack.co/transaction/initialize', [
                'email' => $validated['email'],
                'amount' => $validated['amount'] * 100, // kobo
                'reference' => 'order_' . $validated['order_id'],
                'callback_url' => route('payment.callback'),
                'metadata' => ['order_id' => $validated['order_id']]
            ]);

        $data = $response->json();

        if (!$response->successful() || !isset($data['data']['authorization_url'])) {
            return back()->with('error', 'Payment initialization failed: ' . ($data['message'] ?? 'Unknown error'));
        }

        return redirect($data['data']['authorization_url']);
    }

    public function callback(Request $request)
    {
        $reference = $request->reference;

        $response = Http::withToken(env('PAYSTACK_SECRET_KEY'))
            ->get("https://api.paystack.co/transaction/verify/{$reference}");

        $data = $response->json();

        if ($response->successful() && $data['data']['status'] === 'success') {
            $orderId = str_replace('order_', '', $data['data']['reference']);
            $order = Order::find($orderId);

            if ($order) {
                $order->update(['status' => 'paid']);
                return redirect()->route('orders.show', $order)
                    ->with('success', 'Payment successful! Order #' . $order->id);
            }
        }

        return redirect()->route('checkout.index')
            ->with('error', 'Payment failed. Please try again.');
    }
}