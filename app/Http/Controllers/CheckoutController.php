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
use App\Mail\OrderConfirmed;
use Illuminate\Support\Facades\Mail;



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
    \Log::info('=== GUEST CHECKOUT START ===');
    \Log::info('User: ' . (Auth::check() ? 'LOGGED IN' : 'GUEST'));
    \Log::info('Form Data: ', $request->all());

    $request->validate([
        'shipping_address.name' => 'required|string|max:255',
        'shipping_address.email' => 'required|email|max:255',
        'shipping_address.phone' => 'required|string|max:20',
        'shipping_address.line1' => 'required|string|max:255',
        'shipping_address.city' => 'required|string|max:100',
        'shipping_address.country' => 'required|string|max:100',
        'total' => 'required|numeric|min:0',
    ]);

    \Log::info('VALIDATION PASSED');

    DB::beginTransaction();

    try {
        $user = Auth::user();
        $sessionId = Session::getId();

        // 1. Create Shipping Address
        $shippingData = $request->input('shipping_address');
        $shippingData['phone_number'] = $shippingData['phone'];
        $shippingData['type'] = 'shipping';
        if ($user) {
            $shippingData['user_id'] = $user->id;
        } else {
            $shippingData['session_id'] = $sessionId;
        }
        unset($shippingData['phone']);
        $shippingAddress = Address::create($shippingData);

        $billingAddress = $shippingAddress;

        // 2. Get Cart Items
        $cartItems = CartItem::where(function ($query) use ($user, $sessionId) {
            if ($user) $query->where('user_id', $user->id);
            else $query->where('session_id', $sessionId);
        })->with('product')->get();

        if ($cartItems->isEmpty()) {
            throw new \Exception('Cart is empty');
        }

        // 3. Create Order
        $order = Order::create([
            'user_id' => $user?->id,
            'session_id' => $user ? null : $sessionId,
            'email' => $shippingData['email'],
            'total' => $request->total,
            'status' => 'pending',
            'shipping_address_id' => $shippingAddress->id,
            'billing_address_id' => $billingAddress->id,
        ]);

        // 4. Order Items
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

        // 5. Clear Cart
        if ($user) {
            CartItem::where('user_id', $user->id)->delete();
        } else {
            CartItem::where('session_id', $sessionId)->delete();
        }

        DB::commit();
        \Log::info('TRANSACTION SUCCESS');

        // SEND EMAIL AFTER COMMIT
        Mail::to($order->email)->queue(new OrderConfirmed($order));

        // 6. Redirect to Paystack
        return redirect()->route('payment.initialize', [
            'order_id' => $order->id,
            'email' => $shippingData['email'],
            'amount' => $request->total
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('CHECKOUT FAILED: ' . $e->getMessage());
        return back()->with('error', 'Checkout failed: ' . $e->getMessage())->withInput();
    }
}
}