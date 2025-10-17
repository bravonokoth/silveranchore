<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    // ✅ FIXED: MISSING INDEX METHOD (THIS WAS CAUSING YOUR ERROR!)
    public function index()
    {
        try {
            $user = Auth::user();
            $sessionId = session()->getId();

            // Get cart items for logged-in user OR session
            $cartItems = CartItem::where(function ($query) use ($user, $sessionId) {
                if ($user) {
                    $query->where('user_id', $user->id);
                } else {
                    $query->where('session_id', $sessionId);
                }
            })
            ->with('product') // Eager load product details
            ->get();

            // Calculate totals
            $subtotal = $cartItems->sum(function ($item) {
                return $item->quantity * $item->product->price;
            });
            $total = $subtotal; // Add tax/shipping logic here if needed

            return view('cart.index', compact('cartItems', 'subtotal', 'total'));

        } catch (\Exception $e) {
            Log::error('Cart index error', [
                'error' => $e->getMessage(),
                'user_id' => $user?->id,
                'session_id' => $sessionId,
            ]);
            return view('cart.index', ['cartItems' => collect(), 'subtotal' => 0, 'total' => 0])
                ->with('error', 'Failed to load cart');
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
            ]);

            $sessionId = session()->getId();
            $user = Auth::user();

            $product = Product::findOrFail($validated['product_id']);
            if ($product->stock < $validated['quantity']) {
                return redirect()->back()->with('error', 'Insufficient stock');
            }

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
                CartItem::create([
                    'user_id' => $user ? $user->id : null,
                    'session_id' => $sessionId,
                    'product_id' => $validated['product_id'],
                    'quantity' => $validated['quantity'],
                ]);
            }

            return redirect()->route('cart.index')->with('success', 'Item added to cart!');

        } catch (\Exception $e) {
            Log::error('Cart store error', [
                'error' => $e->getMessage(),
                'user_id' => $user?->id,
                'session_id' => $sessionId,
                'product_id' => $validated['product_id'] ?? null,
            ]);
            return redirect()->back()->with('error', 'Failed to add item to cart');
        }
    }

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
            CartItem::where(function ($query) use ($user, $sessionId) {
                if ($user) {
                    $query->where('user_id', $user->id);
                } else {
                    $query->where('session_id', $sessionId);
                }
            })->delete();

            CartItem::create([
                'user_id' => $user ? $user->id : null,
                'session_id' => $sessionId,
                'product_id' => $productId,
                'quantity' => 1,
            ]);

            return redirect()->route('checkout.index')->with('success', "Quick checkout: {$product->name} added!");

        } catch (\Exception $e) {
            Log::error('Quick checkout error', [
                'error' => $e->getMessage(),
                'user_id' => $user?->id,
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

            return redirect()->route('cart.index')->with('success', 'Cart updated');

        } catch (\Exception $e) {
            Log::error('Cart update error', [
                'error' => $e->getMessage(),
                'user_id' => $user?->id,
                'session_id' => $sessionId,
                'cart_id' => $id,
            ]);
            return redirect()->back()->with('error', 'Failed to update cart');
        }
    }

    // ✅ BONUS: Remove item from cart
    public function remove($id)
    {
        try {
            $user = Auth::user();
            $sessionId = session()->getId();

            $cartItem = CartItem::where('id', $id)
                ->where(function ($query) use ($user, $sessionId) {
                    if ($user) {
                        $query->where('user_id', $user->id);
                    } else {
                        $query->where('session_id', $sessionId);
                    }
                })->firstOrFail();

            $cartItem->delete();

            return redirect()->route('cart.index')->with('success', 'Item removed from cart');

        } catch (\Exception $e) {
            Log::error('Cart remove error', [
                'error' => $e->getMessage(),
                'cart_id' => $id,
            ]);
            return redirect()->back()->with('error', 'Failed to remove item');
        }
    }

    // ✅ BONUS: Clear entire cart
    public function clear()
    {
        try {
            $user = Auth::user();
            $sessionId = session()->getId();

            CartItem::where(function ($query) use ($user, $sessionId) {
                if ($user) {
                    $query->where('user_id', $user->id);
                } else {
                    $query->where('session_id', $sessionId);
                }
            })->delete();

            return redirect()->route('cart.index')->with('success', 'Cart cleared!');

        } catch (\Exception $e) {
            Log::error('Cart clear error', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to clear cart');
        }
    }
}