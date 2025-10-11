@extends('layouts.app')

@section('content')
    <div class="relative min-h-screen bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
        <!-- Hero Section -->
        <section class="relative">
            @if ($banner)
                <div class="relative w-full h-[400px] md:h-[500px] overflow-hidden">
                    <img src="{{ asset('storage/' . $banner->image) }}" alt="{{ $banner->title }}" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black/50 flex flex-col items-center justify-center text-center text-white p-6">
                        <h1 class="text-3xl md:text-4xl font-bold mb-4">{{ $banner->title }}</h1>
                        <p class="text-lg md:text-xl mb-6 max-w-2xl">{{ $banner->description }}</p>
                        <a href="{{ route('products.index') }}" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">Shop Now</a>
                    </div>
                </div>
            @else
                <div class="relative w-full h-[400px] md:h-[500px] bg-blue-600 text-white flex flex-col items-center justify-center text-center p-6">
                    <h1 class="text-3xl md:text-4xl font-bold mb-4">Welcome to SilverAnchorE</h1>
                    <p class="text-lg md:text-xl mb-6 max-w-2xl">Discover premium products with fast and reliable delivery across Kenya.</p>
                    <a href="{{ route('products.index') }}" class="bg-white text-blue-600 py-2 px-4 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">Shop Now</a>
                </div>
            @endif
        </section>

        <!-- Featured Products -->
        <section class="max-w-7xl mx-auto p-6">
            <h2 class="text-2xl font-bold mb-4">Featured Products</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse ($featuredProducts as $product)
                    <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-zinc-900 dark:ring-zinc-800">
                        <img src="{{ $product->media->where('type', 'image')->first() ? asset('storage/' . $product->media->where('type', 'image')->first()->path) : 'https://via.placeholder.com/300' }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-black dark:text-white">{{ $product->name }}</h3>
                            <p class="text-gray-600 dark:text-gray-300">KSh {{ number_format($product->price, 2) }}</p>
                            <div class="mt-2 flex space-x-2">
                                <a href="{{ route('products.show', $product) }}" class="text-blue-600 hover:underline dark:text-blue-400">View</a>
                                <form action="{{ route('cart.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="text-blue-600 hover:underline dark:text-blue-400">Add to Cart</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-600 dark:text-gray-400">No featured products available.</p>
                @endforelse
            </div>
        </section>

        <!-- Categories -->
        <section class="max-w-7xl mx-auto p-6">
            <h2 class="text-2xl font-bold mb-4">Shop by Category</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                @forelse ($categories as $category)
                    <a href="{{ route('categories.show', $category) }}" class="bg-white rounded-lg shadow p-4 text-center hover:bg-gray-100 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:bg-zinc-800">
                        <h3 class="text-lg font-semibold text-black dark:text-white">{{ $category->name }}</h3>
                    </a>
                @empty
                    <p class="text-gray-600 dark:text-gray-400">No categories available.</p>
                @endforelse
            </div>
        </section>

        <!-- Footer -->
        <footer class="max-w-7xl mx-auto py-16 text-center text-sm text-black dark:text-white/70">
            SilverAnchorE &copy; {{ date('Y') }}
        </footer>
    </div>
@endsection