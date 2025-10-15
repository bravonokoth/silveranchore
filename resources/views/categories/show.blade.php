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

            @if ($products->count() > 0)
                <div class="products-grid">
                    @foreach ($products as $product)
                        <div class="product-card">
                            <div class="product-image-wrapper">
                                <img 
                                    src="{{ $product->media->where('type', 'image')->first() ? asset('storage/' . $product->media->where('type', 'image')->first()->path) : 'https://via.placeholder.com/300' }}" 
                                    alt="{{ $product->name }}"
                                    class="product-card-image"
                                >
                                @if($product->stock <= 0)
                                    <div class="out-of-stock-badge">Out of Stock</div>
                                @endif
                            </div>
                            
                            <div class="product-card-content">
                                <h3 class="product-card-title">{{ $product->name }}</h3>
                                <p class="product-card-price">KSh {{ number_format($product->price, 2) }}</p>
                                
                                <div class="product-card-actions">
                                    <a href="{{ route('products.show', $product) }}" class="view-details-btn">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                        View Details
                                    </a>
                                    
                                    @if($product->stock > 0)
                                        <form action="{{ route('cart.store') }}" method="POST" class="add-to-cart-form">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="add-to-cart-btn">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="9" cy="21" r="1"></circle>
                                                    <circle cx="20" cy="21" r="1"></circle>
                                                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                                </svg>
                                                Add to Cart
                                            </button>
                                        </form>
                                    @else
                                        <button class="add-to-cart-btn disabled" disabled>
                                            Out of Stock
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @empty
                <div class="no-products">
                    <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#c0a062" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <p>No products found in this category.</p>
                    <a href="{{ route('products.index') }}" class="browse-all-btn">Browse All Products</a>
                </div>
            @endforelse

            @if($products->count() > 0)
                <div class="pagination-wrapper">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection