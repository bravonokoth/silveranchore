<?php

namespace App\Http\Controllers;

use App\Models\CartItem; 
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

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
        return redirect()->route('orders.store')->withInput($request->all());
    }
}