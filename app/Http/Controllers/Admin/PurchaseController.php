<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\Product;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:admin|super_admin']);
    }

    public function index()
    {
        $purchases = Purchase::with('product')->paginate(20);
        return view('admin.purchases.index', compact('purchases'));
    }

    public function create()
    {
        $products = Product::select('id', 'name')->get();
        return view('admin.purchases.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'cost' => 'required|numeric|min:0',
            'supplier' => 'nullable|string',
            'purchase_date' => 'required|date',
        ]);

        $purchase = Purchase::create($validated);
        $product = Product::find($validated['product_id']);
        $product->stock += $validated['quantity'];
        $product->save();

        return redirect()->route('admin.purchases.index')->with('success', 'Purchase recorded successfully');
    }
}