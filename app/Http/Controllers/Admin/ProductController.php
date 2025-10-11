<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Media;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:admin|super_admin']);
    }

    public function index()
    {
        $products = Product::with(['category', 'media'])->paginate(20);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::select('id', 'name')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:products',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'sku' => 'required|string|unique:products',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $product = Product::create([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?: Str::slug($validated['name']),
            'description' => $validated['description'],
            'price' => $validated['price'],
            'discount_price' => $validated['discount_price'],
            'stock' => $validated['stock'],
            'sku' => $validated['sku'],
            'seo_title' => $validated['seo_title'],
            'seo_description' => $validated['seo_description'],
            'is_active' => $validated['is_active'] ?? true,
            'is_featured' => $validated['is_featured'] ?? false,
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            Media::create([
                'model_type' => 'App\Models\Product',
                'model_id' => $product->id,
                'path' => $imagePath,
                'type' => 'image',
            ]);
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'media']);
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::select('id', 'name')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:products,slug,' . $product->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $product->update([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'discount_price' => $validated['discount_price'],
            'stock' => $validated['stock'],
            'sku' => $validated['sku'],
            'seo_title' => $validated['seo_title'],
            'seo_description' => $validated['seo_description'],
            'is_active' => $validated['is_active'] ?? true,
            'is_featured' => $validated['is_featured'] ?? false,
        ]);

        if ($request->hasFile('image')) {
            $oldMedia = Media::where('model_type', 'App\Models\Product')->where('model_id', $product->id)->first();
            if ($oldMedia) {
                Storage::disk('public')->delete($oldMedia->path);
                $oldMedia->delete();
            }
            $imagePath = $request->file('image')->store('products', 'public');
            Media::create([
                'model_type' => 'App\Models\Product',
                'model_id' => $product->id,
                'path' => $imagePath,
                'type' => 'image',
            ]);
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        $media = Media::where('model_type', 'App\Models\Product')->where('model_id', $product->id)->first();
        if ($media) {
            Storage::disk('public')->delete($media->path);
            $media->delete();
        }
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully');
    }
}