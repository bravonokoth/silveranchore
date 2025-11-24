<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $banner     = Banner::where('is_active', true)->inRandomOrder()->first();
        $categories = Category::with('media')->get();

        // AJAX: Infinite scroll
        if ($request->ajax() || $request->wantsJson()) {
            $type    = $request->get('type', 'new');
            $page    = $request->get('page', 1);
            $perPage = 8;

            $query = Product::where('is_active', true)
                            ->with(['media', 'category']);

            if ($type === 'popular') {
                // Most sold products (based on order_items count)
                $query->select('products.*')
                      ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
                      ->groupBy('products.id')
                      ->orderByRaw('COUNT(order_items.id) DESC, products.created_at DESC');
            } 
            elseif ($type === 'trending') {
                // Recent + slightly more popular (last 30 days)
                $query->where('created_at', '>=', now()->subDays(30))
                      ->orderBy('created_at', 'desc');
            } 
            else {
                // New arrivals
                $query->latest();
            }

            $products = $query->skip(($page - 1) * $perPage)
                              ->take($perPage)
                              ->get();

            $html = '';
            foreach ($products as $product) {
                $html .= view('partials.product-card', compact('product'))->render();
            }

            return response()->json([
                'html'    => $html,
                'hasMore' => $products->count() === $perPage
            ]);
        }

        return view('welcome', compact('banner', 'categories'));
    }

public function loadMore(Request $request)
{
    $type = $request->get('type');
    $page = $request->get('page', 1);

    $perPage = 8;
    $query = Product::where('is_active', true)->with(['media', 'category']);

    if ($type === 'popular') {
        $products = $query->select('products.*')
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->groupBy('products.id')
            ->orderByRaw('COUNT(order_items.id) DESC, products.created_at DESC');
    } elseif ($type === 'trending') {
        $products = $query->where('created_at', '>=', now()->subDays(30))->latest();
    } else {
        $products = $query->latest();
    }

    $products = $products->paginate($perPage, ['*'], 'page', $page);

    // Return ONLY the product cards HTML
    return response()->json([
        'html' => view('partials.product-cards-grid', compact('products'))->render(),
        'hasMore' => $products->hasMorePages()
    ]);
}

}