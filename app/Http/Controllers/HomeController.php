<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $banner = Banner::where('is_active', true)->inRandomOrder()->first();

        // Try to get featured products first
        $featuredProducts = Product::where('is_featured', true)
            ->where('is_active', true)
            ->with(['media', 'category'])
            ->take(8)
            ->get();

        // If no featured products exist, get latest products instead
        if ($featuredProducts->isEmpty()) {
            $featuredProducts = Product::where('is_active', true)
                ->with(['media', 'category'])
                ->latest()
                ->take(8)
                ->get();
        }

        $categories = Category::with('media')->get();

        return view('welcome', compact('banner', 'featuredProducts', 'categories'));
    }
}