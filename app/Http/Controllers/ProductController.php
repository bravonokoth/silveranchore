<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'media'])->where('is_active', true);

        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->category . '%')
                  ->orWhere('slug', 'like', '%' . $request->category . '%');
            });
        }

        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        if ($request->has('in_stock') && $request->in_stock) {
            $query->where('stock', '>', 0);
        }
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->has('is_featured') && $request->is_featured) {
            $query->where('is_featured', true);
        }

        if ($request->has('sort_by')) {
            switch ($request->sort_by) {
                case 'price':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'rating':
                    $query->orderBy('rating', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('id', 'desc');
                    break;
                default:
                    $query->orderBy('name', 'asc');
            }
        }

        $products = $query->paginate(20);

        return view('products.index', compact('products'));
    }

    public function show(Product $product)
    {
        $product->load(['category', 'media']);
        return view('products.show', compact('product'));
    }
}