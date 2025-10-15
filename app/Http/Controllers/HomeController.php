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
        $banner = Banner::where('is_active', true)->inRandomOrder()->first();

        $featuredProducts = Product::where('is_featured', true)
            ->where('is_active', true)
            ->with('media')
            ->take(8)
            ->get();

       
        $categories = Category::with('media') 
            ->get();

        return view('welcome', compact('banner', 'featuredProducts', 'categories'));
    }
}