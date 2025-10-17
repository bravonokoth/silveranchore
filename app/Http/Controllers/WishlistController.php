<?php
namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);
        
        $userId = auth()->id() ?? session('wishlist_user_id', 'guest_' . uniqid());
        session(['wishlist_user_id' => $userId]);
        
        Wishlist::firstOrCreate([
            'user_id' => $userId,
            'product_id' => $request->product_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Added to wishlist!',
            'in_wishlist' => true
        ]);
    }

    public function destroy($productId)
    {
        $userId = auth()->id() ?? session('wishlist_user_id');
        
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