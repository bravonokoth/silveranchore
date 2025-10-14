@extends('layouts.app')

@section('content')
    <div class="relative bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="hero-background"></div>
            <div class="hero-content">
                @if ($banner)
                    <h1>{{ $banner->title }}</h1>
                    <p>{{ $banner->description }}</p>
                @else
                    <h1>Discover Your Perfect Drink</h1>
                    <p>Explore our curated selection of fine wines, spirits, and beers.</p>
                @endif
                <div class="cta-buttons">
                    <a href="{{ route('products.index') }}" class="cta-button">Shop Now</a>
                    <a href="{{ route('about') }}" class="cta-button secondary">Learn More</a>
                </div>
            </div>
        </section>

        <!-- Featured Products Section -->
        <section class="products max-w-7xl mx-auto p-6">
            <h2 class="text-2xl font-bold mb-4">Featured Products</h2>
            <div class="product-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse ($featuredProducts as $product)
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
                                    <button type="submit" class="text-gold hover:underline dark:text-gold-light" data-name="{{ $product->name }}" data-price="{{ $product->price }}">Add to Cart</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-600 dark:text-gray-400">No featured products available.</p>
                @endforelse
            </div>
        </section>

        <!-- Categories Section -->
        <section class="max-w-7xl mx-auto p-6">
            <h2 class="text-2xl font-bold mb-4">Shop by Category</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse ($categories as $category)
                    <a href="{{ route('categories.show', $category) }}" class="category-card bg-white rounded-lg shadow-lg p-4 text-center hover:shadow-xl hover:-translate-y-1 transition-transform duration-300 dark:bg-zinc-900 dark:ring-zinc-800">
                        <h3 class="text-lg font-semibold text-black dark:text-white">{{ $category->name }}</h3>
                    </a>
                @empty
                    <p class="text-gray-600 dark:text-gray-400">No categories available.</p>
                @endforelse
            </div>
        </section>
    </div>
    <script src="{{ asset('script.js') }}"></script>
@endsection