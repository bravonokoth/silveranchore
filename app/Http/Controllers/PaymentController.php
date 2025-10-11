<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only('initialize');
    }

    public function initialize(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'amount' => 'required|numeric|min:0',
            'order_id' => 'required|exists:orders,id',
        ]);

        $response = Http::withToken(env('PAYSTACK_SECRET_KEY'))
            ->post('https://api.paystack.co/transaction/initialize', [
                'email' => $validated['email'],
                'amount' => $validated['amount'] * 100,
                'reference' => 'order_' . $validated['order_id'],
                'callback_url' => route('payment.callback'),
            ]);

        $data = $response->json();

        if (!$response->successful() || !isset($data['data']['authorization_url'])) {
            return redirect()->route('checkout.index')->with('error', 'Payment initialization failed');
        }

        return redirect($data['data']['authorization_url']);
    }

    public function callback(Request $request)
    {
        $reference = $request->reference;

        $response = Http::withToken(env('PAYSTACK_SECRET_KEY'))
            ->get("https://api.paystack.co/transaction/verify/{$reference}");

        $data = $response->json();

        if ($data['data']['status'] === 'success') {
            $orderId = str_replace('order_', '', $data['data']['reference']);
            $order = Order::find($orderId);

            if ($order) {
                $order->update(['status' => 'paid']);
                return redirect()->route('orders.show', $order)->with('success', 'Payment successful');
            }
        }

        return redirect()->route('checkout.index')->with('error', 'Payment failed');
    }
}