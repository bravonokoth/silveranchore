@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">{{ $product->name }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <img src="{{ $product->media->where('type', 'image')->first() ? asset('storage/' . $product->media->where('type', 'image')->first()->path) : 'https://via.placeholder.com/400' }}" alt="{{ $product->name }}" class="w-full h-96 object-cover rounded-lg shadow">
            </div>
            <div>
                <p class="text-gray-600 mb-4">{{ $product->description }}</p>
                <p class="text-xl font-bold mb-4">KSh {{ number_format($product->price, 2) }}</p>
                <p class="text-gray-600 mb-4">Stock: {{ $product->stock > 0 ? $product->stock : 'Out of Stock' }}</p>
                @if ($product->stock > 0)
                    <form action="{{ route('cart.store') }}" method="POST" class="mb-4">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="flex items-center space-x-4">
                            <label for="quantity" class="text-sm font-medium text-gray-700">Quantity:</label>
                            <input type="number" name="quantity" id="quantity" value="1" min="1" max="{{ $product->stock }}" class="w-20 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">Add to Cart</button>
                        </div>
                        @error('quantity')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </form>
                @endif
                <a href="{{ route('products.index') }}" class="text-blue-600 hover:underline">Back to Products</a>
            </div>
        </div>
    </div>
@endsection