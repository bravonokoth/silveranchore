@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Checkout</h2>
        @if (session('error'))
            <div class="bg-red-100 text-red-800 p-4 rounded mb-4">{{ session('error') }}</div>
        @endif
        @if ($cartItems->isEmpty())
            <p class="text-gray-600">Your cart is empty.</p>
            <a href="{{ route('products.index') }}" class="text-blue-600 hover:underline">Continue Shopping</a>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Cart Items -->
                <div>
                    <h3 class="text-xl font-semibold mb-4">Your Cart</h3>
                    <div class="bg-white rounded shadow overflow-hidden">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-200">
                                    <th class="p-3 text-left">Product</th>
                                    <th class="p-3 text-left">Price</th>
                                    <th class="p-3 text-left">Quantity</th>
                                    <th class="p-3 text-left">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cartItems as $item)
                                    @if ($item->product)
                                        <tr>
                                            <td class="p-3">
                                                <div class="flex items-center">
                                                    <img src="{{ $item->product->media->where('type', 'image')->first() ? asset('storage/' . $item->product->media->where('type', 'image')->first()->path) : 'https://via.placeholder.com/150' }}" alt="{{ $item->product->name }}" class="h-16 w-16 object-cover mr-4 rounded">
                                                    <span>{{ $item->product->name }}</span>
                                                </div>
                                            </td>
                                            <td class="p-3">KSh {{ number_format($item->product->price, 2) }}</td>
                                            <td class="p-3">{{ $item->quantity }}</td>
                                            <td class="p-3">KSh {{ number_format($item->product->price * $item->quantity, 2) }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <p class="text-xl font-bold mt-4">Total: KSh {{ number_format($total, 2) }}</p>
                </div>
                <!-- Delivery Address -->
                <div>
                    <h3 class="text-xl font-semibold mb-4">Delivery Address</h3>
                    <form method="POST" action="{{ route('orders.store') }}">
                        @csrf
                        @auth
                            @if (!$addresses->isEmpty())
                                <div class="mb-4">
                                    <label for="address_id" class="block text-sm font-medium text-gray-700">Select Saved Address</label>
                                    <select name="address_id" id="address_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Enter a new address</option>
                                        @foreach ($addresses as $address)
                                            <option value="{{ $address->id }}">{{ $address->first_name }} {{ $address->last_name }} - {{ $address->line1 }}, {{ $address->city }}</option>
                                        @endforeach
                                    </select>
                                    @error('address_id')
                                        <span class="text-red-600 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                        @endauth
                        <div class="space-y-4">
                            <div>
                                <label for="shipping_address.first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                                <input type="text" name="shipping_address[first_name]" id="shipping_address.first_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="{{ old('shipping_address.first_name') }}" required>
                                @error('shipping_address.first_name')
                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="shipping_address.last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                                <input type="text" name="shipping_address[last_name]" id="shipping_address.last_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="{{ old('shipping_address.last_name') }}" required>
                                @error('shipping_address.last_name')
                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="shipping_address.email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" name="shipping_address[email]" id="shipping_address.email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="{{ old('shipping_address.email', auth()->user()->email ?? '') }}" required>
                                @error('shipping_address.email')
                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="shipping_address.phone" class="block text-sm font-medium text-gray-700">Phone</label>
                                <input type="text" name="shipping_address[phone]" id="shipping_address.phone" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="{{ old('shipping_address.phone') }}" required>
                                @error('shipping_address.phone')
                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="shipping_address.line1" class="block text-sm font-medium text-gray-700">Address Line 1</label>
                                <input type="text" name="shipping_address[line1]" id="shipping_address.line1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="{{ old('shipping_address.line1') }}" required>
                                @error('shipping_address.line1')
                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="shipping_address.line2" class="block text-sm font-medium text-gray-700">Address Line 2 (Optional)</label>
                                <input type="text" name="shipping_address[line2]" id="shipping_address.line2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="{{ old('shipping_address.line2') }}">
                                @error('shipping_address.line2')
                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="shipping_address.city" class="block text-sm font-medium text-gray-700">City</label>
                                <input type="text" name="shipping_address[city]" id="shipping_address.city" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="{{ old('shipping_address.city', 'Nairobi') }}" required>
                                @error('shipping_address.city')
                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="shipping_address.state" class="block text-sm font-medium text-gray-700">State (Optional)</label>
                                <input type="text" name="shipping_address[state]" id="shipping_address.state" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="{{ old('shipping_address.state', 'Nairobi') }}">
                                @error('shipping_address.state')
                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="shipping_address.postal_code" class="block text-sm font-medium text-gray-700">Postal Code (Optional)</label>
                                <input type="text" name="shipping_address[postal_code]" id="shipping_address.postal_code" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="{{ old('shipping_address.postal_code') }}">
                                @error('shipping_address.postal_code')
                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="shipping_address.country" class="block text-sm font-medium text-gray-700">Country</label>
                                <input type="text" name="shipping_address[country]" id="shipping_address.country" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="{{ old('shipping_address.country', 'Kenya') }}" required>
                                @error('shipping_address.country')
                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="use_billing" id="use_billing" class="mr-2">
                                <label for="use_billing" class="text-sm font-medium text-gray-700">Use a different billing address</label>
                            </div>
                            <div id="billing-address" class="hidden space-y-4 mt-4">
                                <h3 class="text-xl font-semibold mb-2">Billing Address</h3>
                                <div>
                                    <label for="billing_address.first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                                    <input type="text" name="billing_address[first_name]" id="billing_address.first_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="{{ old('billing_address.first_name') }}">
                                    @error('billing_address.first_name')
                                        <span class="text-red-600 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label for="billing_address.last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                                    <input type="text" name="billing_address[last_name]" id="billing_address.last_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="{{ old('billing_address.last_name') }}">
                                    @error('billing_address.last_name')
                                        <span class="text-red-600 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label for="billing_address.email" class="block text-sm font-medium text-gray-700">Email</label>
                                    <input type="email" name="billing_address[email]" id="billing_address.email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="{{ old('billing_address.email') }}">
                                    @error('billing_address.email')
                                        <span class="text-red-600 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label for="billing_address.phone" class="block text-sm font-medium text-gray-700">Phone</label>
                                    <input type="text" name="billing_address[phone]" id="billing_address.phone" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="{{ old('billing_address.phone') }}">
                                    @error('billing_address.phone')
                                        <span class="text-red-600 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label for="billing_address.line1" class="block text-sm font-medium text-gray-700">Address Line 1</label>
                                    <input type="text" name="billing_address[line1]" id="billing_address.line1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="{{ old('billing_address.line1') }}">
                                    @error('billing_address.line1')
                                        <span class="text-red-600 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label for="billing_address.line2" class="block text-sm font-medium text-gray-700">Address Line 2 (Optional)</label>
                                    <input type="text" name="billing_address[line2]" id="billing_address.line2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="{{ old('billing_address.line2') }}">
                                </div>
                                <div>
                                    <label for="billing_address.city" class="block text-sm font-medium text-gray-700">City</label>
                                    <input type="text" name="billing_address[city]" id="billing_address.city" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="{{ old('billing_address.city') }}">
                                    @error('billing_address.city')
                                        <span class="text-red-600 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label for="billing_address.state" class="block text-sm font-medium text-gray-700">State (Optional)</label>
                                    <input type="text" name="billing_address[state]" id="billing_address.state" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="{{ old('billing_address.state') }}">
                                </div>
                                <div>
                                    <label for="billing_address.postal_code" class="block text-sm font-medium text-gray-700">Postal Code (Optional)</label>
                                    <input type="text" name="billing_address[postal_code]" id="billing_address.postal_code" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="{{ old('billing_address.postal_code') }}">
                                </div>
                                <div>
                                    <label for="billing_address.country" class="block text-sm font-medium text-gray-700">Country</label>
                                    <input type="text" name="billing_address[country]" id="billing_address.country" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="{{ old('billing_address.country') }}">
                                    @error('billing_address.country')
                                        <span class="text-red-600 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <input type="hidden" name="total" value="{{ $total }}">
                            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">Place Order</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
    <script>
        document.getElementById('use_billing')?.addEventListener('change', function () {
            document.getElementById('billing-address').classList.toggle('hidden', !this.checked);
        });
        document.getElementById('address_id')?.addEventListener('change', function () {
            const inputs = document.querySelectorAll('[name^="shipping_address"]');
            inputs.forEach(input => input.required = !this.value);
        });
    </script>
@endsection