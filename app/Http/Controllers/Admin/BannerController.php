<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // ADD THIS

class BannerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:admin|super_admin']);
    }

    public function index()
    {
        $banners = Banner::paginate(20);
        return view('admin.banner.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banner.create');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        $banners = Banner::where('title', 'like', "%{$query}%")
            ->orWhere('link', 'like', "%{$query}%")
            ->paginate(20)
            ->appends(['query' => $query]);

        return view('admin.banner.index', compact('banners'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image_path' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'link' => 'nullable|url',
            'is_active' => 'boolean',
        ]);

        $validated['image_path'] = $request->file('image_path')->store('banners', 'public');
        Banner::create($validated);

        return redirect()->route('admin.banner.index')->with('success', 'Banner created successfully');
    }

    // ✅ ADD THESE 3 METHODS:
    
    /** Show single banner */
    public function show(Banner $banner)
    {
        return view('admin.banner.show', compact('banner'));
    }

    /** Edit banner form */
    public function edit(Banner $banner)
    {
        return view('admin.banner.edit', compact('banner'));
    }

    /** Update banner */
    public function update(Request $request, Banner $banner)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'link' => 'nullable|url',
            'is_active' => 'boolean',
        ]);

        // Delete old image if new one uploaded
        if ($request->hasFile('image_path')) {
            Storage::disk('public')->delete($banner->image_path);
            $validated['image_path'] = $request->file('image_path')->store('banners', 'public');
        }

        $banner->update($validated);

        return redirect()->route('admin.banner.index')->with('success', 'Banner updated successfully');
    }

    /** Delete banner + image */
    public function destroy(Banner $banner)
    {
        // Delete image from storage
        if ($banner->image_path) {
            Storage::disk('public')->delete($banner->image_path);
        }
        
        $banner->delete();

        return redirect()->route('admin.banner.index')->with('success', 'Banner deleted successfully');
    }
}