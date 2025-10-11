@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Your Cart</h2>
        @if (session('success'))
            <div class="bg-green-100 text-green-800 p-4 rounded mb-4">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 text-red-800 p-4 rounded mb-4">{{ session('error') }}</div>
        @endif
        @if ($cartItems->isEmpty())
            <p class="text-gray-600">Your cart is empty.</p>
            <a href="{{ route('products.index') }}" class="text-blue-600 hover:underline">Continue Shopping</a>
        @else
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="p-3 text-left">Product</th>
                            <th class="p-3 text-left">Price</th>
                            <th class="p-3 text-left">Quantity</th>
                            <th class="p-3 text-left">Total</th>
                            <th class="p-3 text-left">Actions</th>
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
                                    <td class="p-3">
                                        <form action="{{ route('cart.update', $item) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}" class="w-20 border-gray-300 rounded-md shadow-sm">
                                            <button type="submit" class="text-blue-600 hover:underline">Update</button>
                                        </form>
                                    </td>
                                    <td class="p-3">KSh {{ number_format($item->product->price * $item->quantity, 2) }}</td>
                                    <td class="p-3">
                                        <form action="{{ route('cart.destroy', $item) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Remove this item?')">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-6 flex justify-between items-center">
                <p class="text-xl font-bold">Total: KSh {{ number_format($total, 2) }}</p>
                <a href="{{ route('checkout.index') }}" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700">Proceed to Checkout</a>
            </div>
        @endif
    </div>
@endsection