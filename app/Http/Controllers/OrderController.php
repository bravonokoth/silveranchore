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

// 🔥 ADD THESE IMPORTS FOR PESAPAL
use Knox\Pesapal\Facades\Pesapal;  // Or use Knox\Pesapal\Pesapal if no facade

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
        // 🔥 DEBUG LOG (UNCHANGED)
        \Log::info('=== ORDER STORE DEBUG ===');
        \Log::info('User: ' . (auth()->check() ? 'LOGGED IN' : 'GUEST'));
        \Log::info('Form Data: ', $request->all());

        $user = auth()->user();
        $sessionId = Session::getId();

        // ✅ SINGLE NAME VALIDATION! (UNCHANGED)
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

        \Log::info('Calculated Total: KSh ' . $calculatedTotal);

        if (abs($calculatedTotal - $request->total) > 0.01) {
            return redirect()->route('checkout.index')->with('error', 'Total mismatch');
        }

        foreach ($cartItems as $item) {
            if (!$item->product || $item->product->stock < $item->quantity) {
                return redirect()->route('checkout.index')->with('error', "Insufficient stock for product: {$item->product->name}");
            }
        }

        \Log::info('🔥 STARTING TRANSACTION');
        DB::beginTransaction();
        try {
            // 1. Handle shipping address ✅ FIXED WITH FULL DEBUG! (UNCHANGED)
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

            // 2. Handle billing address ✅ FIXED! (UNCHANGED)
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

            // 3. Create order (UNCHANGED)
            \Log::info('🔥 Creating Order...');
            $order = Order::create([
                'user_id' => $user?->id,
                'session_id' => $user ? null : $sessionId,
                'email' => $user?->email ?? $validated['shipping_address']['email'],
                'total' => $calculatedTotal,
                'status' => 'pending',
                'shipping_address_id' => $shippingAddress->id,
                'billing_address_id' => $billingAddress->id,
            ]);
            \Log::info('✅ Order Created: #' . $order->id);

            // 4. Save order items & reduce stock (UNCHANGED)
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

            // 🔥 PESAPAL INTEGRATION (REPLACES PAYSTACK)
            \Log::info('🔥 INITIALIZING PESAPAL...');
            $email = $user?->email ?? $validated['shipping_address']['email'];
            $phone = $validated['shipping_address']['phone'];
            $name = explode(' ', $validated['shipping_address']['name'])[0] ?? 'Customer';  // First name for PesaPal
            
            // Generate unique transaction ID (like Paystack reference)
            $transactionId = 'order_' . $order->id . '_' . time();
            
            \Log::info('PesaPal Email: ' . $email);
            \Log::info('PesaPal Amount: KSh ' . $calculatedTotal);
            \Log::info('PesaPal Transaction ID: ' . $transactionId);

            // Submit to PesaPal (Creates payment session)
            $pesapalResponse = Pesapal::submit([
                'amount' => $calculatedTotal,
                'description' => 'Order #' . $order->id . ' - Silveranchore Purchase',
                'transaction_id' => $transactionId,
                'currency' => 'KES',
                'firstname' => $name,
                'lastname' => substr($validated['shipping_address']['name'], strlen($name)) ?: 'User',
                'email' => $email,
                'phonenumber' => $phone,
                'reference' => $order->id,  // For your tracking
            ]);

            \Log::info('PesaPal Response: ', $pesapalResponse);

            if (!$pesapalResponse || !isset($pesapalResponse['url'])) {
                \Log::error('❌ PesaPal Failed: ' . json_encode($pesapalResponse));
                DB::rollBack();
                return redirect()->route('checkout.index')->with('error', 'Payment initialization failed: ' . ($pesapalResponse['error'] ?? 'Unknown error'));
            }

            // 5. Clear cart ✅ GUEST SESSION! (UNCHANGED)
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

            \Log::info('🚀 REDIRECTING TO PESAPAL: ' . $pesapalResponse['url']);
            return redirect($pesapalResponse['url']);  // To PesaPal iframe/payment page

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('💥 EXACT ERROR: ' . $e->getMessage());
            \Log::error('💥 LINE NUMBER: ' . $e->getLine());
            \Log::error('💥 FILE: ' . $e->getFile());
            return redirect()->route('checkout.index')->with('error', 'ERROR: ' . $e->getMessage());
        }
    }

    // 🔥 ADD THESE NEW METHODS FOR PESAPAL CALLBACK & IPN
    public function callback(Request $request)
    {
        \Log::info('=== PESAPAL CALLBACK ===');
        \Log::info('Callback Data: ', $request->all());

        // Query payment status using tracking ID from PesaPal
        $trackingId = $request->input('pesapal_transaction_tracking_id') ?? $request->input('order_tracking_id');
        if (!$trackingId) {
            return redirect()->route('checkout.index')->with('error', 'Invalid callback');
        }

        $status = Pesapal::queryPaymentStatus($trackingId);
        \Log::info('PesaPal Status: ', $status);

        if ($status['payment_status'] === 'COMPLETED') {
            $orderId = $status['order_reference'] ?? explode('_', $trackingId)[1];  // Extract from reference
            $order = Order::findOrFail($orderId);
            $order->update([
                'status' => 'paid',
                'transaction_id' => $trackingId,
            ]);
            \Log::info('✅ Payment Completed for Order #' . $orderId);
            return redirect()->route('orders.success', $orderId)->with('success', 'Payment successful! Order confirmed.');
        }

        \Log::error('❌ Payment Failed: ' . $status['payment_status']);
        return redirect()->route('checkout.index')->with('error', 'Payment failed: ' . ($status['reason'] ?? 'Try again'));
    }

    public function ipn(Request $request)
    {
        \Log::info('=== PESAPAL IPN ===');
        \Log::info('IPN Data: ', $request->all());

        $trackingId = $request->input('pesapal_transaction_tracking_id');
        if (!$trackingId) {
            \Log::warning('Invalid IPN - No tracking ID');
            return response('OK', 200);  // Acknowledge to PesaPal
        }

        $status = Pesapal::queryPaymentStatus($trackingId);
        \Log::info('IPN Status: ', $status);

        if ($status['payment_status'] === 'COMPLETED') {
            $orderId = $status['order_reference'] ?? explode('_', $trackingId)[1];
            $order = Order::find($orderId);
            if ($order && $order->status === 'pending') {
                $order->update([
                    'status' => 'paid',
                    'transaction_id' => $trackingId,
                ]);
                // Optional: Send email, dispatch job
                \Log::info('✅ IPN Updated Order #' . $orderId);
            }
        }

        return response('OK', 200);  // Always acknowledge IPN
    }

    // Optional: Success View Method
    public function success(Order $order)
    {
        if ($order->status !== 'paid') {
            abort(404);
        }
        $order->load(['items.product', 'shippingAddress']);
        return view('orders.success', compact('order'));  // Create this view if needed
    }
}