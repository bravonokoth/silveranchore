<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Address;
use App\Models\Notification;
use App\Events\NotificationSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Unicodeveloper\Paystack\Facades\Paystack;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('store');
    }

    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with(['items.product', 'shippingAddress'])
            ->latest()
            ->get();

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);
        $order->load(['items.product', 'shippingAddress', 'billingAddress']);
        return view('orders.show', compact('order'));
    }

    public function store(Request $request)
    {
        Log::info('=== ORDER STORE DEBUG ===');
        Log::info('User: ' . (auth()->check() ? 'LOGGED IN (ID: ' . auth()->id() . ')' : 'GUEST'));
        Log::info('Form Data: ', $request->all());

        $user = auth()->user();
        $sessionId = Session::getId();

        try {
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
                'shipping_address.phone_number' => 'required_without:address_id|string|max:20|regex:/^2547[0-9]{8}$/', // Fixed to phone_number
                'shipping_address.line1' => 'required_without:address_id|string|max:255',
                'shipping_address.line2' => 'nullable|string|max:255',
                'shipping_address.city' => 'required_without:address_id|string|max:100',
                'shipping_address.state' => 'nullable|string|max:100',
                'shipping_address.postal_code' => 'nullable|string|max:20',
                'shipping_address.country' => 'required_without:address_id|string|max:100',
                'use_billing' => 'nullable|boolean',
                'billing_address.name' => 'required_if:use_billing,1|string|max:255',
                'billing_address.email' => 'required_if:use_billing,1|email|max:255',
                'billing_address.phone_number' => 'required_if:use_billing,1|string|max:20|regex:/^2547[0-9]{8}$/',
                'billing_address.line1' => 'required_if:use_billing,1|string|max:255',
                'billing_address.line2' => 'nullable|string|max:255',
                'billing_address.city' => 'required_if:use_billing,1|string|max:100',
                'billing_address.state' => 'nullable|string|max:100',
                'billing_address.postal_code' => 'nullable|string|max:20',
                'billing_address.country' => 'required_if:use_billing,1|string|max:100',
                'total' => 'required|numeric|min:0',
                'payment_method' => 'required|in:paystack,pesapal',
            ]);

            Log::info('✅ VALIDATION PASSED');
            Log::info('📝 Validated Data:', $validated);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('❌ VALIDATION FAILED');
            Log::error('Validation Errors:', $e->errors());
            return redirect()->route('checkout.index')
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Please fix the validation errors.');
        }

        // Get cart items
        $cartItems = $user
            ? CartItem::where('user_id', $user->id)->with('product')->get()
            : CartItem::where('session_id', $sessionId)->with('product')->get();

        Log::info('Cart Items Found: ' . $cartItems->count());

        if ($cartItems->isEmpty()) {
            Log::error('❌ CART EMPTY');
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Calculate total
        $calculatedTotal = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        Log::info('Calculated Total: KSh ' . number_format($calculatedTotal, 2));

        // Validate total
        if (abs($calculatedTotal - $request->total) > 0.01) {
            Log::error('❌ TOTAL MISMATCH: Expected ' . $calculatedTotal . ', Got ' . $request->total);
            return redirect()->route('checkout.index')->with('error', 'Price mismatch detected. Please refresh and try again.');
        }

        // Check stock availability
        foreach ($cartItems as $item) {
            if (!$item->product || $item->product->stock < $item->quantity) {
                Log::error("❌ INSUFFICIENT STOCK: {$item->product->name}");
                return redirect()->route('checkout.index')
                    ->with('error', "Insufficient stock for {$item->product->name}. Available: {$item->product->stock}");
            }
        }

        Log::info('🔥 STARTING TRANSACTION');
        DB::beginTransaction();

        try {
            // 1. Handle shipping address
            $shippingAddress = null;
            if ($user && $request->address_id) {
                $shippingAddress = Address::where('id', $request->address_id)
                    ->where('user_id', $user->id)
                    ->firstOrFail();
                Log::info('✅ Using saved shipping address: ' . $shippingAddress->id);
            } else {
                $shippingData = [
                    'user_id' => $user?->id,
                    'session_id' => $user ? null : $sessionId,
                    'type' => 'shipping',
                    'name' => $validated['shipping_address']['name'],
                    'email' => $validated['shipping_address']['email'],
                    'phone_number' => $validated['shipping_address']['phone_number'],
                    'line1' => $validated['shipping_address']['line1'],
                    'line2' => $validated['shipping_address']['line2'] ?? null,
                    'city' => $validated['shipping_address']['city'],
                    'state' => $validated['shipping_address']['state'] ?? null,
                    'postal_code' => $validated['shipping_address']['postal_code'] ?? null,
                    'country' => $validated['shipping_address']['country'],
                ];

                Log::info('🔍 Creating shipping address: ', $shippingData);
                $shippingAddress = Address::create($shippingData);
                Log::info('✅ New shipping address created: ' . $shippingAddress->id);
            }

            // 2. Handle billing address
            $billingAddress = $shippingAddress;
            if ($request->use_billing && !empty($validated['billing_address']['name'])) {
                $billingData = [
                    'user_id' => $user?->id,
                    'session_id' => $user ? null : $sessionId,
                    'type' => 'billing',
                    'name' => $validated['billing_address']['name'],
                    'email' => $validated['billing_address']['email'],
                    'phone_number' => $validated['billing_address']['phone_number'],
                    'line1' => $validated['billing_address']['line1'],
                    'line2' => $validated['billing_address']['line2'] ?? null,
                    'city' => $validated['billing_address']['city'],
                    'state' => $validated['billing_address']['state'] ?? null,
                    'postal_code' => $validated['billing_address']['postal_code'] ?? null,
                    'country' => $validated['billing_address']['country'],
                ];

                Log::info('🔍 Creating billing address: ', $billingData);
                $billingAddress = Address::create($billingData);
                Log::info('✅ Billing address created: ' . $billingAddress->id);
            }

            // 3. Create order with payment fields
            $orderEmail = $user?->email ?? $validated['shipping_address']['email'];

            Log::info('🔥 Creating Order...');
            $order = Order::create([
                'user_id' => $user?->id,
                'session_id' => $user ? null : $sessionId,
                'email' => $orderEmail,
                'total' => $calculatedTotal,
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => $validated['payment_method'],
                'shipping_address_id' => $shippingAddress->id,
                'billing_address_id' => $billingAddress->id,
            ]);
            Log::info('✅ Order Created: #' . $order->id);

            // 4. Save order items & reduce stock
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);
                $item->product->decrement('stock', $item->quantity);
            }
            Log::info('✅ Order Items Created & Stock Reduced');

            // 5. Clear cart
            $user
                ? CartItem::where('user_id', $user->id)->delete()
                : CartItem::where('session_id', $sessionId)->delete();
            Log::info('✅ Cart Cleared');

            DB::commit();
            Log::info('✅ TRANSACTION COMMITTED');

            // 6. Fire notification event
            event(new NotificationSent(
                "Order #{$order->id} created successfully.",
                $order->email,
                $order->user_id,
                $order->session_id
            ));

            // 7. Initialize payment
            Log::info('🔥 INITIALIZING PAYMENT METHOD: ' . $validated['payment_method']);
            if ($validated['payment_method'] === 'paystack') {
                return $this->initializePaystack($order, $orderEmail, $calculatedTotal);
            } elseif ($validated['payment_method'] === 'pesapal') {
                return $this->initializePesapal($order);
            }

            return redirect()->route('orders.show', $order)->with('info', 'Order created. Please complete payment.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('💥 ORDER CREATION FAILED');
            Log::error('💥 ERROR: ' . $e->getMessage());
            Log::error('💥 LINE: ' . $e->getLine());
            Log::error('💥 FILE: ' . $e->getFile());
            Log::error('💥 TRACE: ' . $e->getTraceAsString());
            return redirect()->route('checkout.index')
                ->with('error', 'Failed to create order: ' . $e->getMessage());
        }
    }

    private function initializePaystack(Order $order, string $email, float $amount)
    {
        try {
            Log::info('🔥 INITIALIZING PAYSTACK');
            Log::info('Order ID: ' . $order->id);
            Log::info('Email: ' . $email);
            Log::info('Amount: KSh ' . number_format($amount, 2));

            $paymentData = [
                'amount' => $amount * 100,
                'reference' => Paystack::genTranxRef(),
                'email' => $email,
                'callback_url' => route('payment.callback'),
                'metadata' => [
                    'order_id' => $order->id,
                    'customer_email' => $email,
                    'custom_fields' => [
                        [
                            'display_name' => 'Order ID',
                            'variable_name' => 'order_id',
                            'value' => $order->id,
                        ],
                    ],
                ],
            ];

            $order->update(['payment_reference' => $paymentData['reference']]);

            Log::info('✅ Paystack Data Prepared: ', $paymentData);
            Log::info('🚀 REDIRECTING TO PAYSTACK...');
            return Paystack::getAuthorizationUrl($paymentData)->redirectNow();
        } catch (\Exception $e) {
            Log::error('💥 PAYSTACK INITIALIZATION FAILED: ' . $e->getMessage());
            return redirect()->route('checkout.index')
                ->with('error', 'Payment initialization failed. Please try again.');
        }
    }

    private function initializePesapal(Order $order)
    {
        Log::info('🔥 INITIALIZING PESAPAL for Order #' . $order->id);
        return redirect()->route('orders.show', $order)
            ->with('info', 'PesaPal integration coming soon. Order created successfully.');
    }

    public function success(Order $order)
    {
        if ($order->user_id && $order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to order.');
        }
        if (!$order->user_id && $order->session_id !== Session::getId()) {
            abort(403, 'Unauthorized access to order.');
        }
        return view('orders.success', compact('order'));
    }

    public function callback(Request $request)
    {
        Log::info('=== PESAPAL CALLBACK ===', $request->all());
        return redirect()->route('home')->with('info', 'Payment processing...');
    }

    public function ipn(Request $request)
    {
        Log::info('=== PESAPAL IPN ===', $request->all());
        return response()->json(['status' => 'received']);
    }
}