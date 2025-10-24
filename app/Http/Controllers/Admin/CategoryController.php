<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:admin|super_admin']);
    }

    public function index()
    {
        $categories = Category::with('media')->withCount('products')->paginate(20);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $categories = Category::select('id', 'name')->get();
        return view('admin.categories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $validated['slug'] = Str::slug($request->input('name') . '-' . time());
        
        // Don't include image in the create - we'll handle it separately
        $category = Category::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'],
            'parent_id' => $validated['parent_id'],
        ]);

        // Handle image upload using Media table (same as products)
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories', 'public');
            Media::create([
                'model_type' => 'App\Models\Category',
                'model_id' => $category->id,
                'path' => $imagePath,
                'type' => 'image',
            ]);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully');
    }

    public function show(Category $category)
    {
        $category->load(['products', 'media']);
        return view('admin.categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        $categories = Category::select('id', 'name')->where('id', '!=', $category->id)->get();
        $category->load('media');
        return view('admin.categories.edit', compact('category', 'categories'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $validated['slug'] = Str::slug($request->input('name') . '-' . $category->id);
        
        $category->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'],
            'parent_id' => $validated['parent_id'],
        ]);

        // Handle image upload using Media table (same as products)
        if ($request->hasFile('image')) {
            // Delete old media if exists
            $oldMedia = Media::where('model_type', 'App\Models\Category')
                ->where('model_id', $category->id)
                ->first();
            
            if ($oldMedia) {
                Storage::disk('public')->delete($oldMedia->path);
                $oldMedia->delete();
            }

            // Store new image
            $imagePath = $request->file('image')->store('categories', 'public');
            Media::create([
                'model_type' => 'App\Models\Category',
                'model_id' => $category->id,
                'path' => $imagePath,
                'type' => 'image',
            ]);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully');
    }

    public function destroy(Category $category)
    {
        // Delete associated media
        $media = Media::where('model_type', 'App\Models\Category')
            ->where('model_id', $category->id)
            ->first();
        
        if ($media) {
            Storage::disk('public')->delete($media->path);
            $media->delete();
        }

        $category->delete();
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully');
    }

    public function search(Request $request)
    {
        $query = $request->input('search');
        $categories = Category::with('media')
            ->withCount('products')
            ->where('name', 'like', "%$query%")
            ->orWhere('description', 'like', "%$query%")
            ->paginate(20)
            ->appends(['search' => $query]);
            
        return view('admin.categories.index', compact('categories'));
    }
}