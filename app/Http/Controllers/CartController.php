<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    /**
     * Display the cart page
     */
    public function index()
    {
        try {
            $cartData = $this->getCartData();

            return view('cart.index', [
                'cartItems' => $cartData['items'],
                'subtotal'  => $cartData['subtotal'],
                'total'     => $cartData['subtotal'], // Extend later with tax/shipping
            ]);
        } catch (\Exception $e) {
            Log::error('Cart index error', [
                'error'      => $e->getMessage(),
                'user_id'    => Auth::id(),
                'session_id' => session()->getId(),
            ]);

            return view('cart.index', [
                'cartItems' => collect(),
                'subtotal'  => 0,
                'total'     => 0,
            ])->with('error', 'Failed to load your cart.');
        }
    }

    /**
     * Add item to cart
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        $sessionId = session()->getId();
        $product = Product::findOrFail($request->product_id);

        if ($product->stock < $request->quantity) {
            return redirect()->back()->with('error', 'Sorry, not enough stock available.');
        }

        $cartItem = CartItem::where('product_id', $product->id)
            ->where(function ($query) use ($user, $sessionId) {
                $user ? $query->where('user_id', $user->id)
                      : $query->where('session_id', $sessionId);
            })
            ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $request->quantity;
            if ($product->stock < $newQuantity) {
                return redirect()->back()->with('error', 'Cannot add more than available stock.');
            }
            $cartItem->quantity = $newQuantity;
            $cartItem->save();
        } else {
            CartItem::create([
                'user_id'     => $user?->id,
                'session_id'  => $sessionId,
                'product_id'  => $product->id,
                'quantity'    => $request->quantity,
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Item added to cart!');
    }

    /**
     * Quick checkout - replace cart with single item
     */
    public function quickCheckout($productId)
    {
        $product = Product::findOrFail($productId);

        if ($product->stock < 1) {
            return redirect()->back()->with('error', 'This product is out of stock.');
        }

        $this->clearCart();

        CartItem::create([
            'user_id'     => Auth::id(),
            'session_id'  => session()->getId(),
            'product_id'  => $product->id,
            'quantity'    => 1,
        ]);

        return redirect()->route('checkout.index')
            ->with('success', "Quick checkout: {$product->name} added!");
    }

    /**
     * Update cart item quantity (supports AJAX)
     */
    public function update(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        $cartItem = $this->findCartItem($id);
        $product = $cartItem->product;

        if ($product->stock < $request->quantity) {
            $message = 'Only ' . $product->stock . ' item(s) in stock.';

            return $request->wantsJson()
                ? response()->json(['success' => false, 'message' => $message], 400)
                : redirect()->back()->with('error', $message);
        }

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        $response = ['success' => true, 'message' => 'Cart updated!'];

        return $request->wantsJson()
            ? response()->json($response)
            : redirect()->route('cart.index')->with('success', 'Cart updated!');
    }

    /**
     * Remove item from cart (supports AJAX)
     */
    public function remove(Request $request, $id)
    {
        $this->findCartItem($id)->delete();

        $response = ['success' => true, 'message' => 'Item removed from cart.'];

        return $request->wantsJson()
            ? response()->json($response)
            : redirect()->route('cart.index')->with('success', 'Item removed!');
    }

    /**
     * Clear entire cart
     */
    public function clear()
    {
        $this->clearCart();

        return redirect()->route('cart.index')->with('success', 'Cart cleared!');
    }

    /**
     * API Endpoint: Get cart contents (for mini-cart, AJAX updates)
     */
    public function getCartItems()
    {
        try {
            $data = $this->getCartData();

            return response()->json([
                'items'    => $data['items'],
                'subtotal' => $data['subtotal'],
                'count'    => $data['items']->count(),
                'success'  => true,
            ]);
        } catch (\Exception $e) {
            Log::error('API getCartItems failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'items'   => [],
                'subtotal'=> 0,
                'count'   => 0,
                'message' => 'Failed to load cart'
            ], 500);
        }
    }

    // =================================================================
    // Helper Methods (DRY)
    // =================================================================

    private function getCartData()
    {
        $user = Auth::user();
        $sessionId = session()->getId();

        $cartItems = CartItem::with('product.media')
            ->where(function ($q) use ($user, $sessionId) {
                $user ? $q->where('user_id', $user->id)
                      : $q->where('session_id', $sessionId);
            })
            ->get();

        $items = $cartItems->map(function ($item) {
            if (!$item->product) return null;

            $image = $item->product->media
                ->where('type', 'image')
                ->first()?->path;

            $price = $item->product->price;
            $total = $price * $item->quantity;

            return [
                'id'       => $item->id,
                'name'     => $item->product->name,
                'price'    => $price,
                'quantity' => $item->quantity,
                'stock'    => $item->product->stock,
                'image'    => $image ? asset('storage/' . $image)
                                     : 'https://via.placeholder.com/80',
                'total'    => $total,
                'url'      => route('products.show', $item->product),
            ];
        })->filter();

        $subtotal = $items->sum('total');

        return compact('items', 'subtotal');
    }

    private function findCartItem($id)
    {
        $user = Auth::user();
        $sessionId = session()->getId();

        return CartItem::where('id', $id)
            ->where(function ($q) use ($user, $sessionId) {
                $user ? $q->where('user_id', $user->id)
                      : $q->where('session_id', $sessionId);
            })
            ->firstOrFail();
    }

    private function clearCart()
    {
        $user = Auth::user();
        $sessionId = session()->getId();

        CartItem::where(function ($q) use ($user, $sessionId) {
            $user ? $q->where('user_id', $user->id)
                  : $q->where('session_id', $sessionId);
        })->delete();
    }
}