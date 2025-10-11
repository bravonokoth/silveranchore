<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $sessionId = Session::getId();

        $cartItems = $user
            ? CartItem::where('user_id', $user->id)->with(['product.media'])->get()
            : CartItem::where('session_id', $sessionId)->with(['product.media'])->get();

        $total = $cartItems->sum(function ($item) {
            return $item->product ? $item->product->price * $item->quantity : 0;
        });

        return view('cart.index', compact('cartItems', 'total'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
            ]);

            $user = auth()->user();
            $sessionId = Session::getId();

            $product = Product::find($validated['product_id']);
            if (!$product || $product->stock < $validated['quantity']) {
                return redirect()->route('cart.index')->with('error', 'Insufficient stock for product: ' . ($product ? $product->name : 'Unknown'));
            }

            CartItem::updateOrCreate(
                [
                    'user_id' => $user ? $user->id : null,
                    'session_id' => $user ? null : $sessionId,
                    'product_id' => $validated['product_id'],
                ],
                ['quantity' => $validated['quantity']]
            );

            return redirect()->route('cart.index')->with('success', 'Item added to cart');
        } catch (\Exception $e) {
            Log::error('Cart store error', [
                'error' => $e->getMessage(),
                'user_id' => $user ? $user->id : null,
                'session_id' => $sessionId,
                'product_id' => $validated['product_id'] ?? null,
            ]);
            return redirect()->route('cart.index')->with('error', 'Failed to add item to cart');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'quantity' => 'required|integer|min:1',
            ]);

            $user = auth()->user();
            $sessionId = Session::getId();

            $cartItem = CartItem::where('id', $id)
                ->where($user ? 'user_id' : 'session_id', $user ? $user->id : $sessionId)
                ->firstOrFail();

            if (!$cartItem->product) {
                $cartItem->delete();
                return redirect()->route('cart.index')->with('error', 'Cart item removed due to missing product');
            }

            if ($cartItem->product->stock < $validated['quantity']) {
                return redirect()->route('cart.index')->with('error', 'Insufficient stock for product: ' . $cartItem->product->name);
            }

            $cartItem->update(['quantity' => $validated['quantity']]);

            return redirect()->route('cart.index')->with('success', 'Cart item updated');
        } catch (\Exception $e) {
            Log::error('Cart update error', [
                'error' => $e->getMessage(),
                'cart_item_id' => $id,
            ]);
            return redirect()->route('cart.index')->with('error', 'Failed to update cart item');
        }
    }

    public function destroy($id)
    {
        try {
            $user = auth()->user();
            $sessionId = Session::getId();

            $cartItem = CartItem::where('id', $id)
                ->where($user ? 'user_id' : 'session_id', $user ? $user->id : $sessionId)
                ->firstOrFail();
            $cartItem->delete();

            return redirect()->route('cart.index')->with('success', 'Cart item removed');
        } catch (\Exception $e) {
            Log::error('Cart destroy error', [
                'error' => $e->getMessage(),
                'cart_item_id' => $id,
            ]);
            return redirect()->route('cart.index')->with('error', 'Failed to remove cart item');
        }
    }

    public function clear()
    {
        try {
            $user = auth()->user();
            $sessionId = Session::getId();

            CartItem::where($user ? 'user_id' : 'session_id', $user ? $user->id : $sessionId)->delete();

            return redirect()->route('cart.index')->with('success', 'Cart cleared');
        } catch (\Exception $e) {
            Log::error('Cart clear error', [
                'error' => $e->getMessage(),
                'user_id' => $user ? $user->id : null,
                'session_id' => $sessionId,
            ]);
            return redirect()->route('cart.index')->with('error', 'Failed to clear cart');
        }
    }
}