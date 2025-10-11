<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:admin|super_admin']);
    }

    public function index()
    {
        $categories = Category::withCount('products')->paginate(20);
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
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        $validated['slug'] = Str::slug($request->input('name') . '-' . time());
        Category::create($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully');
    }

    public function show(Category $category)
    {
        $category->load('products');
        return view('admin.categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        $categories = Category::select('id', 'name')->where('id', '!=', $category->id)->get();
        return view('admin.categories.edit', compact('category', 'categories'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        $validated['slug'] = Str::slug($request->input('name') . '-' . $category->id);
        $category->update($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully');
    }

    public function search(Request $request)
    {
        $query = $request->input('search');
        $categories = Category::withCount('products')
            ->where('name', 'like', "%$query%")
            ->orWhere('description', 'like', "%$query%")
            ->get();
        return view('admin.categories.index', compact('categories'));
    }
}