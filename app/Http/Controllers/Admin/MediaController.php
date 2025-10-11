<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:admin|super_admin']);
    }

    public function index()
    {
        $inventories = Inventory::with('product')->paginate(20);
        return view('admin.inventories.index', compact('inventories'));
    }

    public function create()
    {
        $products = Product::select('id', 'name')->get();
        return view('admin.inventories.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer',
            'type' => 'required|in:adjustment,restock,sale',
            'notes' => 'nullable|string',
        ]);

        $inventory = Inventory::create($validated);
        $product = Product::find($validated['product_id']);
        $product->stock += $validated['quantity'];
        $product->save();

        return redirect()->route('admin.inventories.index')->with('success', 'Inventory updated successfully');
    }
}