<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
   public function index(Request $request)
    {
        $query = Category::with(['media', 'products'])  
            ->withCount('products');  // ✅ Product count

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
        }

        if ($request->has('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        }

        
        $categories = $query->get();

        return view('categories.index', compact('categories'));
    }

   public function show(Category $category)
{
    $products = $category->products()
        ->where('is_active', true)
        ->with('media')
        ->paginate(12); // ✅ adds pagination

    return view('categories.show', compact('category', 'products'));
}

}