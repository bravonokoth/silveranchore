@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto p-6">
        <!-- Search and Filters -->
        <div class="flex flex-col sm:flex-row justify-between mb-6 space-y-4 sm:space-y-0 sm:space-x-4">
            <form action="{{ route('products.index') }}" method="GET" class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." class="w-full border-gray-300 rounded-md shadow-sm focus:ring-gold focus:border-gold p-2 dark:bg-zinc-900 dark:border-gray-600 dark:text-white dark:focus:ring-gold-light dark:focus:border-gold-light">
            </form>
            <select name="category" onchange="window.location.href=this.value" class="border-gray-300 rounded-md shadow-sm focus:ring-gold focus:border-gold p-2 dark:bg-zinc-900 dark:border-gray-600 dark:text-white dark:focus:ring-gold-light dark:focus:border-gold-light">
                <option value="{{ route('products.index') }}">All Categories</option>
                @foreach ($categories as $category)
                    <option value="{{ route('products.index', ['category' => $category->slug]) }}" {{ request('category') == $category->slug ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Product Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse ($products as $product)
                <div class="product-card bg-white rounded-lg shadow-lg overflow-hidden dark:bg-zinc-900 dark:ring-zinc-800 transition-transform duration-300 hover:shadow-xl hover:-translate-y-1">
                    <img src="{{ $product->media->where('type', 'image')->first() ? asset('storage/' . $product->media->where('type', 'image')->first()->path) : 'https://via.placeholder.com/300' }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-black dark:text-white">{{ $product->name }}</h3>
                        <p class="price text-gray-600 dark:text-gray-300 mt-2">KSh {{ number_format($product->price, 2) }}</p>
                        <div class="mt-4 flex space-x-2">
                            <a href="{{ route('products.show', $product) }}" class="text-gold hover:underline dark:text-gold-light">View</a>
                            <form action="{{ route('cart.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="text-gold hover:underline dark:text-gold-light">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-gray-600 dark:text-gray-400">No products found.</p>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $products->links() }}
        </div>
    </div>
@endsection