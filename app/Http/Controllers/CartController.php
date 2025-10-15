<?php

namespace App\Http\Controllers;

use App\Models\CartItem; // ✅ CORRECT: CartItem model
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
            ]);

            $sessionId = session()->getId();
            $user = Auth::user(); // ✅ FIXED: Defined $user

            $product = Product::findOrFail($validated['product_id']);
            if ($product->stock < $validated['quantity']) {
                return redirect()->back()->with('error', 'Insufficient stock');
            }

            // ✅ FIXED: CartItem model
            $cartItem = CartItem::where('product_id', $validated['product_id'])
                ->where(function ($query) use ($user, $sessionId) {
                    if ($user) {
                        $query->where('user_id', $user->id);
                    } else {
                        $query->where('session_id', $sessionId);
                    }
                })->first();

            if ($cartItem) {
                $cartItem->quantity += $validated['quantity'];
                $cartItem->save();
            } else {
                CartItem::create([ // ✅ FIXED: CartItem::create
                    'user_id' => $user ? $user->id : null,
                    'session_id' => $sessionId,
                    'product_id' => $validated['product_id'],
                    'quantity' => $validated['quantity'],
                ]);
            }

            return redirect()->back()->with('success', 'Item added to cart');
        } catch (\Exception $e) {
            Log::error('Cart store error', [
                'error' => $e->getMessage(),
                'user_id' => $user ? $user->id : null,
                'session_id' => $sessionId,
                'product_id' => $validated['product_id'] ?? null,
            ]);
            return redirect()->back()->with('error', 'Failed to add item to cart');
        }
    }

    // ✅ NEW: quickCheckout method (uses CartItem)
    public function quickCheckout($productId)
    {
        try {
            $product = Product::findOrFail($productId);
            if ($product->stock < 1) {
                return redirect()->back()->with('error', 'Product out of stock');
            }

            $sessionId = session()->getId();
            $user = Auth::user();

            // Clear cart and add only this product
            CartItem::where(function ($query) use ($user, $sessionId) { // ✅ FIXED: CartItem
                if ($user) {
                    $query->where('user_id', $user->id);
                } else {
                    $query->where('session_id', $sessionId);
                }
            })->delete();

            CartItem::create([ // ✅ FIXED: CartItem::create
                'user_id' => $user ? $user->id : null,
                'session_id' => $sessionId,
                'product_id' => $productId,
                'quantity' => 1,
            ]);

            return redirect()->route('checkout.index')->with('success', "Quick checkout: {$product->name} added!");
        } catch (\Exception $e) {
            Log::error('Quick checkout error', [
                'error' => $e->getMessage(),
                'user_id' => $user ? $user->id : null,
                'product_id' => $productId,
            ]);
            return redirect()->back()->with('error', 'Quick checkout failed');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'quantity' => 'required|integer|min:1',
            ]);

            $user = Auth::user();
            $sessionId = session()->getId();

            // ✅ FIXED: CartItem model
            $cartItem = CartItem::where('id', $id)
                ->where(function ($query) use ($user, $sessionId) {
                    if ($user) {
                        $query->where('user_id', $user->id);
                    } else {
                        $query->where('session_id', $sessionId);
                    }
                })->firstOrFail();

            $product = Product::findOrFail($cartItem->product_id);
            if ($product->stock < $validated['quantity']) {
                return redirect()->back()->with('error', 'Insufficient stock');
            }

            $cartItem->quantity = $validated['quantity'];
            $cartItem->save();

            return redirect()->back()->with('success', 'Cart updated');
        } catch (\Exception $e) {
            Log::error('Cart update error', [
                'error' => $e->getMessage(),
                'user_id' => $user ? $user->id : null,
                'session_id' => $sessionId,
                'cart_id' => $id,
            ]);
            return redirect()->back()->with('error', 'Failed to update cart');
        }
    }
}