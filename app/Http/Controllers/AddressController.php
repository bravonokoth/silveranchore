<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Restrict all methods to authenticated users
    }

    public function index()
    {
        $addresses = Address::where('user_id', Auth::id())->get();
        return view('addresses.index', compact('addresses'));
    }

    public function create()
    {
        return view('addresses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:shipping,billing',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:20',
            'line1' => 'required|string|max:255',
            'line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'required|string|max:100',
        ]);

        Address::create(array_merge($validated, ['user_id' => Auth::id()]));

        return redirect()->route('addresses.index')->with('success', 'Address added successfully.');
    }

    public function edit(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        return view('addresses.edit', compact('address'));
    }

    public function update(Request $request, Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'type' => 'required|in:shipping,billing',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:20',
            'line1' => 'required|string|max:255',
            'line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'required|string|max:100',
        ]);

        $address->update($validated);

        return redirect()->route('addresses.index')->with('success', 'Address updated successfully.');
    }

    public function destroy(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $address->delete();
        return redirect()->route('addresses.index')->with('success', 'Address deleted successfully.');
    }
}