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

    public function search(Request $request)
    {
        $query = $request->input('query');

        $inventories = Inventory::with('product')
            ->where('type', 'like', "%{$query}%")
            ->orWhere('quantity', 'like', "%{$query}%")
            ->orWhereHas('product', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('product_code', 'like', "%{$query}%");
            })
            ->orWhere('notes', 'like', "%{$query}%")
            ->paginate(20)
            ->appends(['query' => $query]);

        return view('admin.inventories.index', compact('inventories'));
    }

    // ✅ ADD THESE 4 METHODS:

    /** Show single inventory record */
    public function show(Inventory $inventory)
    {
        $inventory->load('product');
        return view('admin.inventories.show', compact('inventory'));
    }

    /** Edit inventory form */
    public function edit(Inventory $inventory)
    {
        $products = Product::select('id', 'name')->get();
        $inventory->load('product');
        return view('admin.inventories.edit', compact('inventory', 'products'));
    }

    /** Update inventory */
    public function update(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer',
            'type' => 'required|in:adjustment,restock,sale',
            'notes' => 'nullable|string',
        ]);

        // Calculate stock difference
        $stockDiff = $validated['quantity'] - $inventory->quantity;
        
        $inventory->update($validated);
        
        // Update product stock
        $product = Product::find($validated['product_id']);
        $product->stock += $stockDiff;
        $product->save();

        return redirect()->route('admin.inventories.index')->with('success', 'Inventory updated successfully');
    }

    /** Delete inventory record */
    public function destroy(Inventory $inventory)
    {
        // Update product stock (reverse the quantity)
        $product = $inventory->product;
        $product->stock -= $inventory->quantity;
        $product->save();

        $inventory->delete();

        return redirect()->route('admin.inventories.index')->with('success', 'Inventory record deleted successfully');
    }
}