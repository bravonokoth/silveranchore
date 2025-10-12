<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:admin|super_admin']);
    }

    public function index()
    {
        $coupons = Coupon::paginate(20);
        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('admin.coupons.create');
    }

    public function search(Request $request)
{
    $query = $request->input('query');

    $coupons = Coupon::where('code', 'like', "%{$query}%")
        ->orWhere('discount', 'like', "%{$query}%")
        ->orWhere('expires_at', 'like', "%{$query}%")
        ->paginate(20)
        ->appends(['query' => $query]);

    return view('admin.coupons.index', compact('coupons'));
}


    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:coupons',
            'discount' => 'required|numeric|min:0|max:100',
            'expires_at' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        Coupon::create($validated);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created successfully');
    }
}