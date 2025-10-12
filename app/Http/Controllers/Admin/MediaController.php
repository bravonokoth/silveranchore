<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:admin|super_admin']);
    }

    public function index()
    {
        $media = Media::paginate(20);
        return view('admin.media.index', compact('media'));
    }

    public function create()
    {
        return view('admin.media.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'model_type' => 'required|in:App\Models\Product,App\Models\Category',
            'model_id' => 'required|integer',
            'path' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'type' => 'required|in:image,video',
        ]);

        $validated['path'] = $request->file('path')->store('media', 'public');
        Media::create($validated);

        return redirect()->route('admin.media.index')->with('success', 'Media uploaded successfully');
    }

    public function search(Request $request)
{
    $query = $request->input('query');

    $media = Media::where('model_type', 'like', "%{$query}%")
        ->orWhere('type', 'like', "%{$query}%")
        ->orWhere('path', 'like', "%{$query}%")
        ->paginate(20)
        ->appends(['query' => $query]);

    return view('admin.media.index', compact('media'));
}

}