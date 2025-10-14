@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4 text-black dark:text-white">{{ $product->name }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="product-card bg-white rounded-lg shadow-lg overflow-hidden dark:bg-zinc-900 dark:ring-zinc-800">
                <img src="{{ $product->media->where('type', 'image')->first() ? asset('storage/' . $product->media->where('type', 'image')->first()->path) : 'https://via.placeholder.com/400' }}" alt="{{ $product->name }}" class="w-full h-96 object-cover">
            </div>
            <div class="flex flex-col justify-between">
                <div>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">{{ $product->description }}</p>
                    <p class="text-xl font-bold mb-4 text-black dark:text-white">KSh {{ number_format($product->price, 2) }}</p>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">Stock: {{ $product->stock > 0 ? $product->stock : 'Out of Stock' }}</p>
                </div>
                @if ($product->stock > 0)
                    <form action="{{ route('cart.store') }}" method="POST" class="mb-4">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="flex items-center space-x-4">
                            <label for="quantity" class="text-sm font-medium text-gray-700 dark:text-gray-300">Quantity:</label>
                            <input type="number" name="quantity" id="quantity" value="1" min="1" max="{{ $product->stock }}" class="w-20 border-gray-300 rounded-md shadow-sm focus:ring-gold focus:border-gold dark:bg-zinc-900 dark:border-gray-600 dark:text-white dark:focus:ring-gold-light dark:focus:border-gold-light">
                            <button type="submit" class="bg-gold text-white py-2 px-4 rounded-md hover:bg-gold-dark focus:outline-none focus:ring-2 focus:ring-gold">Add to Cart</button>
                        </div>
                        @error('quantity')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </form>
                @endif
                <a href="{{ route('products.index') }}" class="text-gold hover:underline dark:text-gold-light">Back to Products</a>
            </div>
        </div>
    </div>
@endsection