@extends('layouts.app')

@section('content')
    <div class="products-wrapper">
        <div class="products-container">
            <div class="products-header">
                <h2>{{ $category->name }}</h2>
                <div class="breadcrumb">
                    <a href="{{ route('home') }}">Home</a>
                    <span>/</span>
                    <span>{{ $category->name }}</span>
                </div>
            </div>

            <div class="products-grid">
                @forelse ($products as $product)
                    <div class="product-card">
                        <!-- Product Image - Clickable -->
                        <a href="{{ route('products.show', $product) }}" class="product-image-wrapper">
                            <img 
                                src="{{ $product->media->where('type', 'image')->first() 
                                    ? asset('storage/' . $product->media->where('type', 'image')->first()->path) 
                                    : 'https://via.placeholder.com/300' }}" 
                                alt="{{ $product->name }}"
                                class="product-card-image"
                            >

                            @if ($product->stock <= 0)
                                <div class="out-of-stock-badge">Out of Stock</div>
                            @else
                                <div class="in-stock-badge">In Stock</div>
                            @endif
                        </a>
                        
                        <div class="product-card-content">
                            <!-- Split Layout: Name/Category LEFT, Price/Stock RIGHT -->
                            <div class="product-info-grid">
                                <!-- LEFT SIDE: Product Name & Category -->
                                <div class="product-info-left">
                                    <h3 class="product-card-title">
                                        <a href="{{ route('products.show', $product) }}">
                                            {{ $product->name }}
                                        </a>
                                    </h3>
                                    <a href="{{ route('categories.show', $product->category_id) }}" class="product-category-link">
                                        {{ $product->category?->name ?? 'Uncategorized' }}
                                    </a>
                                </div>

                                <!-- RIGHT SIDE: Price & Stock -->
                                <div class="product-info-right">
                                    <p class="product-card-price">
                                        @if ($product->discount_price && $product->discount_price < $product->price)
                                            <span class="current-price">Ksh {{ number_format($product->discount_price, 0) }}</span>
                                            <span class="original-price">Ksh {{ number_format($product->price, 0) }}</span>
                                        @else
                                            <span class="current-price">Ksh {{ number_format($product->price, 0) }}</span>
                                        @endif
                                    </p>
                                    
                                    <div class="product-stock-info {{ $product->stock <= 0 ? 'out-of-stock' : '' }}">
                                        @if($product->stock > 0)
                                            <i class="fas fa-check-circle"></i> {{ $product->stock }} in stock
                                        @else
                                            <i class="fas fa-times-circle"></i> Out of stock
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="product-card-actions">
                                <div class="button-row">
                                    @if ($product->stock > 0)
                                        <form action="{{ route('cart.store') }}" method="POST" class="add-to-cart-form">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="add-to-cart-btn">
                                                <i class="fas fa-shopping-cart"></i>
                                                Add to Cart
                                            </button>
                                        </form>

                                        <a href="{{ route('checkout.quick', $product->id) }}" class="buy-now-btn">
                                            <i class="fas fa-bolt"></i>
                                            Buy Now
                                        </a>
                                    @else
                                        <button class="add-to-cart-btn disabled" disabled>
                                            <i class="fas fa-ban"></i>
                                            Out of Stock
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="no-products">
                        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" 
                             viewBox="0 0 24 24" fill="none" stroke="#c0a062" 
                             stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <p>No products found in this category.</p>
                        <a href="{{ route('products.index') }}" class="browse-all-btn">
                            Browse All Products
                        </a>
                    </div>
                @endforelse
            </div>

            @if ($products->count() > 0)
                <div class="pagination-wrapper">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection