<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * Get or generate a unique user ID for wishlist (authenticated or guest).
     */
    private function getWishlistUserId()
    {
        if (auth()->check()) {
            return auth()->id();
        }

        // Retrieve or generate guest user ID
        $userId = session('wishlist_user_id');
        if (!$userId) {
            $userId = 'guest_' . uniqid();
            session(['wishlist_user_id' => $userId]);
        }

        return $userId;
    }

    /**
     * Display the user's wishlist.
     */
    public function index()
    {
        $userId = $this->getWishlistUserId();

        // Fetch wishlist items for the user
        $wishlistItems = Wishlist::where('user_id', $userId)
            ->with('product') // Assumes a relationship with the Product model
            ->get();

        return view('wishlist.index', compact('wishlistItems'));
    }

    /**
     * Add a product to the wishlist.
     */
    public function store(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);

        $userId = $this->getWishlistUserId();

        $wishlist = Wishlist::firstOrCreate([
            'user_id' => $userId,
            'product_id' => $request->product_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => $wishlist->wasRecentlyCreated ? 'Added to wishlist!' : 'Already in wishlist!',
            'in_wishlist' => true
        ]);
    }

    /**
     * Remove a product from the wishlist.
     */
    public function destroy($productId)
    {
        $userId = $this->getWishlistUserId();

        Wishlist::where([
            'user_id' => $userId,
            'product_id' => $productId
        ])->delete();

        return response()->json([
            'success' => true,
            'message' => 'Removed from wishlist!',
            'in_wishlist' => false
        ]);
    }
}