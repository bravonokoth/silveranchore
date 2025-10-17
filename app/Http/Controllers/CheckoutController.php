<?php

namespace App\Http\Controllers;

use App\Models\CartItem; 
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $sessionId = Session::getId();

        $cartItems = CartItem::where(function ($query) use ($user, $sessionId) {
                if ($user) {
                    $query->where('user_id', $user->id);
                } else {
                    $query->where('session_id', $sessionId);
                }
            })
            ->with(['product.media'])
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Cart is empty');
        }

        $total = $cartItems->sum(function ($item) {
            return $item->product ? $item->product->price * $item->quantity : 0;
        });

        $addresses = $user ? Address::where('user_id', $user->id)->where('type', 'shipping')->get() : collect();

        return view('checkout.index', compact('cartItems', 'total', 'addresses'));
    }

    public function store(Request $request)
    {
        // 🔥 DEBUG LOG
        \Log::info('=== GUEST CHECKOUT START ===');
        \Log::info('User: ' . (Auth::check() ? 'LOGGED IN' : 'GUEST'));
        \Log::info('Form Data: ', $request->all());

        // ✅ SINGLE NAME VALIDATION!
        $request->validate([
            'shipping_address.name' => 'required|string|max:255',        // FIXED!
            'shipping_address.email' => 'required|email|max:255',
            'shipping_address.phone' => 'required|string|max:20',
            'shipping_address.line1' => 'required|string|max:255',
            'shipping_address.city' => 'required|string|max:100',
            'shipping_address.country' => 'required|string|max:100',
            'total' => 'required|numeric|min:0',
        ]);

        \Log::info('✅ VALIDATION PASSED');

        DB::beginTransaction();

        try {
            $user = Auth::user();
            $sessionId = Session::getId();
            \Log::info('Session ID: ' . $sessionId);

            // 1. Create Shipping Address ✅ SINGLE NAME!
            $shippingData = $request->input('shipping_address');
            $shippingData['phone_number'] = $shippingData['phone'];
            $shippingData['type'] = 'shipping';
            
            if ($user) {
                $shippingData['user_id'] = $user->id;
            } else {
                $shippingData['session_id'] = $sessionId;
            }
            unset($shippingData['phone']);
            
            \Log::info('Shipping Data: ', $shippingData);
            $shippingAddress = Address::create($shippingData);
            \Log::info('✅ Address Created: ' . $shippingAddress->id);

            // 2. Billing (simple for GUEST)
            $billingAddress = $shippingAddress;

            // 3. Get Cart Items ✅ GUEST SESSION!
            $cartItems = CartItem::where(function ($query) use ($user, $sessionId) {
                if ($user) $query->where('user_id', $user->id);
                else $query->where('session_id', $sessionId);
            })->with('product')->get();

            \Log::info('Cart Items: ' . $cartItems->count());
            if ($cartItems->isEmpty()) {
                throw new \Exception('Cart is empty');
            }

            // 4. Create Order
            $order = Order::create([
                'user_id' => $user?->id,
                'session_id' => $user ? null : $sessionId,
                'email' => $shippingData['email'],
                'total' => $request->total,
                'status' => 'pending',
                'shipping_address_id' => $shippingAddress->id,
                'billing_address_id' => $billingAddress->id,
            ]);
            \Log::info('✅ Order Created: ' . $order->id);

            // 5. Order Items
            foreach ($cartItems as $item) {
                if ($item->product) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price' => $item->product->price,
                    ]);
                }
            }

            // 6. Clear Cart
            if ($user) {
                CartItem::where('user_id', $user->id)->delete();
            } else {
                CartItem::where('session_id', $sessionId)->delete();
            }
            \Log::info('✅ Cart Cleared');

            DB::commit();
            \Log::info('✅ SUCCESS! Redirecting to Paystack');

            // 7. GUEST PAYSTACK ✅ FIXED!
            return redirect()->route('payment.initialize', [
                'order_id' => $order->id,
                'email' => $shippingData['email'],
                'amount' => $request->total
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('❌ FAILED: ' . $e->getMessage());
            return back()->with('error', 'ERROR: ' . $e->getMessage())->withInput();
        }
    }
}