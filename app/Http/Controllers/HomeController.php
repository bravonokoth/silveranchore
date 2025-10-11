<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the home page with a banner, featured products, and categories.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Fetch a random active banner
        $banner = Banner::where('is_active', true)->inRandomOrder()->first();

        // Fetch up to 8 featured products with their media
        $featuredProducts = Product::where('is_featured', true)
            ->where('is_active', true)
            ->with('media')
            ->take(8)
            ->get();

        // Fetch all categories
        $categories = Category::all();

        return view('welcome', compact('banner', 'featuredProducts', 'categories'));
    }
}