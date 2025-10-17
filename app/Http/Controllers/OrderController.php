<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Events\NotificationSent;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('store'); // Allow guests to store orders
    }

    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with(['items.product', 'shippingAddress'])
            ->get();

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);
        $order->load(['items.product', 'shippingAddress']);
        return view('orders.show', compact('order'));
    }

    public function store(Request $request)
    {
        // 🔥 DEBUG LOG
        \Log::info('=== ORDER STORE DEBUG ===');
        \Log::info('User: ' . (auth()->check() ? 'LOGGED IN' : 'GUEST'));
        \Log::info('Form Data: ', $request->all());

        $user = auth()->user();
        $sessionId = Session::getId();

        // ✅ SINGLE NAME VALIDATION!
        $validated = $request->validate([
            'address_id' => ['nullable', 'exists:addresses,id', function ($attribute, $value, $fail) use ($user) {
                if ($user && !Address::where('id', $value)->where('user_id', $user->id)->exists()) {
                    $fail('The selected address is invalid.');
                }
                if (!$user && $value) {
                    $fail('Guest users cannot select saved addresses.');
                }
            }],
            'shipping_address.name' => 'required_without:address_id|string|max:255',
            'shipping_address.email' => 'required_without:address_id|email|max:255',
            'shipping_address.phone' => 'required_without:address_id|string|max:20',
            'shipping_address.line1' => 'required_without:address_id|string|max:255',
            'shipping_address.line2' => 'nullable|string|max:255',
            'shipping_address.city' => 'required_without:address_id|string|max:100',
            'shipping_address.state' => 'nullable|string|max:100',
            'shipping_address.postal_code' => 'nullable|string|max:20',
            'shipping_address.country' => 'required_without:address_id|string|max:100',
            'use_billing' => 'nullable|boolean',
            'billing_address.name' => 'required_if:use_billing,1|string|max:255',
            'billing_address.email' => 'required_if:use_billing,1|email|max:255',
            'billing_address.phone' => 'required_if:use_billing,1|string|max:20',
            'billing_address.line1' => 'required_if:use_billing,1|string|max:255',
            'billing_address.line2' => 'nullable|string|max:255',
            'billing_address.city' => 'required_if:use_billing,1|string|max:100',
            'billing_address.state' => 'nullable|string|max:100',
            'billing_address.postal_code' => 'nullable|string|max:20',
            'billing_address.country' => 'required_if:use_billing,1|string|max:100',
            'total' => 'required|numeric|min:0',
        ]);

        \Log::info('✅ VALIDATION PASSED');

        $cartItems = $user
            ? CartItem::where('user_id', $user->id)->with('product')->get()
            : CartItem::where('session_id', $sessionId)->with('product')->get();

        \Log::info('Cart Items Found: ' . $cartItems->count());

        if ($cartItems->isEmpty()) {
            \Log::error('❌ CART EMPTY');
            return redirect()->route('cart.index')->with('error', 'Cart is empty');
        }

        $calculatedTotal = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        if (abs($calculatedTotal - $request->total) > 0.01) {
            return redirect()->route('checkout.index')->with('error', 'Total mismatch');
        }

        foreach ($cartItems as $item) {
            if (!$item->product || $item->product->stock < $item->quantity) {
                return redirect()->route('checkout.index')->with('error', "Insufficient stock for product: {$item->product->name}");
            }
        }

        DB::beginTransaction();
        try {
            // 1. Handle shipping address ✅ FIXED WITH DEBUG!
            $shippingAddress = null;
            if ($user && $request->address_id) {
                $shippingAddress = Address::where('id', $request->address_id)
                    ->where('user_id', $user->id)
                    ->firstOrFail();
                \Log::info('✅ Using saved address: ' . $shippingAddress->id);
            } else {
                $shippingData = array_merge(
                    $validated['shipping_address'],
                    [
                        'user_id' => $user?->id,
                        'session_id' => $user ? null : $sessionId,
                        'type' => 'shipping',
                        'phone_number' => $validated['shipping_address']['phone'],
                    ]
                );
                unset($shippingData['phone']); // ✅ CRITICAL!
                \Log::info('🔍 Shipping Data: ', $shippingData); // ✅ DEBUG!
                $shippingAddress = Address::create($shippingData);
                \Log::info('✅ New address created: ' . $shippingAddress->id);
            }

            // 2. Handle billing address ✅ FIXED!
            $billingAddress = $shippingAddress;
            if ($request->use_billing) {
                $billingData = array_merge(
                    $validated['billing_address'],
                    [
                        'user_id' => $user?->id,
                        'session_id' => $user ? null : $sessionId,
                        'type' => 'billing',
                        'phone_number' => $validated['billing_address']['phone'],
                    ]
                );
                unset($billingData['phone']); // ✅ CRITICAL!
                \Log::info('🔍 Billing Data: ', $billingData); // ✅ DEBUG!
                $billingAddress = Address::create($billingData);
                \Log::info('✅ Billing address created: ' . $billingAddress->id);
            }

            // 3. Create order
            $order = Order::create([
                'user_id' => $user?->id,
                'session_id' => $user ? null : $sessionId,
                'email' => $user?->email ?? $validated['shipping_address']['email'],
                'total' => $calculatedTotal,
                'status' => 'pending',
                'shipping_address_id' => $shippingAddress->id,
                'billing_address_id' => $billingAddress->id,
            ]);
            \Log::info('✅ Order Created: ' . $order->id);

            // Save order items & reduce stock
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);
                $item->product->decrement('stock', $item->quantity);
            }
            \Log::info('✅ Order Items & Stock Updated');

            // Initialize Paystack payment ✅ GUEST WORKS!
            $response = Http::withToken(env('PAYSTACK_SECRET_KEY'))
                ->post('https://api.paystack.co/transaction/initialize', [
                    'email' => $user?->email ?? $validated['shipping_address']['email'],
                    'amount' => $calculatedTotal * 100,
                    'reference' => 'order_' . $order->id,
                    'callback_url' => route('payment.callback'),
                ]);

            $data = $response->json();

            if (!$response->successful() || !isset($data['data']['authorization_url'])) {
                \Log::error('❌ Paystack Failed: ' . json_encode($data));
                DB::rollBack();
                return redirect()->route('checkout.index')->with('error', 'Payment initialization failed');
            }

            // Clear cart ✅ GUEST SESSION!
            $user
                ? CartItem::where('user_id', $user->id)->delete()
                : CartItem::where('session_id', $sessionId)->delete();
            \Log::info('✅ Cart Cleared');

            DB::commit();
            \Log::info('✅ TRANSACTION COMMITTED');

            event(new NotificationSent(
                "Order #{$order->id} created successfully.",
                $order->email,
                $order->user_id,
                $order->session_id
            ));

            \Log::info('🚀 REDIRECTING TO PAYSTACK: ' . $data['data']['authorization_url']);
            return redirect($data['data']['authorization_url']);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('❌ ORDER FAILED: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->route('checkout.index')->with('error', 'Order creation failed: ' . $e->getMessage());
        }
    }
}