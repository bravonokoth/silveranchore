<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\CartItem;

class SyncGuestCart
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $sessionId = Session::getId();

            // Move any guest items that belong to the current session into the logged-in user
            CartItem::where('session_id', $sessionId)
                ->whereNull('user_id')
                ->get()
                ->each(function ($item) {
                    $existing = CartItem::firstOrNew([
                        'user_id'    => Auth::id(),
                        'product_id' => $item->product_id,
                    ]);

                    if ($existing->exists) {
                        $existing->increment('quantity', $item->quantity);
                    } else {
                        $item->user_id    = Auth::id();
                        $item->session_id = null;
                        $item->save();
                    }

                    $item->delete(); // remove the guest row
                });
        }

        return $next($request);
    }
}