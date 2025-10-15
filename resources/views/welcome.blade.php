@extends('layouts.app')

@section('content')
<!-- Display Success Message -->
@if (session('success'))
    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    </div>
@endif

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-background">
        <img class="hero-image" src="{{ asset('images/pexels.jpg') }}" alt="Hero Image">
    </div>
    <div class="hero-content">
        <h1 class="text-4xl font-bold text-white">Welcome to The Liquor Cabinet</h1>
        <p class="text-lg text-white mb-6">Discover our premium selection of fine liquors and accessories.</p>
        <div class="cta-buttons flex gap-4">
            <a href="{{ route('products.index') }}" class="cta-button">Shop Now</a>
            <a href="{{ route('categories.index') }}" class="cta-button secondary">Browse Categories</a>
        </div>
    </div>
</section>

<!-- Category Section -->
<section class="max-w-7xl mx-auto py-8">
    <h2 class="text-2xl font-semibold mb-6 text-gray-800">Our Categories</h2>
    <div class="js-slick-carousel u-slick u-slick--gutters-3 u-slick--equal-height"
         data-slides-show="4"
         data-slides-scroll="3"
         data-infinite="true"
         data-pagi-classes="text-center u-slick__pagination mt-7 mb-0"
         data-responsive='[{
           "breakpoint": 992,
           "settings": { "slidesToShow": 3 }
         }, {
           "breakpoint": 720,
           "settings": { "slidesToShow": 2 }
         }, {
           "breakpoint": 480,
           "settings": { "slidesToShow": 1 }
         }]'>
        @php
            $categories = $categories->isEmpty() ? collect([
                (object)['id' => 1, 'name' => 'Whiskey'],
                (object)['id' => 2, 'name' => 'Vodka'],
                (object)['id' => 3, 'name' => 'Gin'],
                (object)['id' => 4, 'name' => 'Rum'],
            ]) : $categories;
        @endphp
        @foreach ($categories as $category)
            <div class="js-slide">
                <div class="category-card">
                    <h3 class="text-lg font-semibold text-gray-800">{{ $category->name }}</h3>
                    <a href="{{ route('categories.show', $category->id) }}" class="text-gold hover:underline">View Products</a>
                </div>
            </div>
        @endforeach
    </div>
    <div class="text-center u-slick__pagination mt-7 mb-0"></div>
</section>

<!-- Product Section (WITH NEW BUTTONS) -->
<section class="products max-w-7xl mx-auto py-8">
    <h2 class="text-2xl font-semibold mb-6 text-gray-800">Featured Products</h2>
    <div class="js-slick-carousel u-slick u-slick--gutters-3 u-slick--equal-height"
         data-slides-show="4"
         data-slides-scroll="3"
         data-infinite="true"
         data-pagi-classes="text-center u-slick__pagination mt-7 mb-0"
         data-responsive='[{
           "breakpoint": 992,
           "settings": { "slidesToShow": 3 }
         }, {
           "breakpoint": 720,
           "settings": { "slidesToShow": 2 }
         }, {
           "breakpoint": 480,
           "settings": { "slidesToShow": 1 }
         }]'>
        @php
            $featuredProducts = $featuredProducts->isEmpty() ? collect([
                (object)[
                    'id' => 1, 'name' => 'Premium Whiskey', 'price' => 49.99, 'original_price' => 59.99,
                    'category_id' => 1, 'category' => (object)['name' => 'Whiskey'],
                    'media' => collect([(object)['url' => asset('images/placeholder.jpg')]]),
                    'status' => 'new', 'rating' => 4, 'review_count' => 25, 'stock' => 10
                ],
                (object)[
                    'id' => 2, 'name' => 'Classic Vodka', 'price' => 29.99, 'original_price' => null,
                    'category_id' => 2, 'category' => (object)['name' => 'Vodka'],
                    'media' => collect([(object)['url' => asset('images/placeholder.jpg')]]),
                    'status' => null, 'rating' => 3, 'review_count' => 10, 'stock' => 20
                ],
                (object)[
                    'id' => 3, 'name' => 'Artisan Gin', 'price' => 39.99, 'original_price' => 45.99,
                    'category_id' => 3, 'category' => (object)['name' => 'Gin'],
                    'media' => collect([(object)['url' => asset('images/placeholder.jpg')]]),
                    'status' => 'sold_out', 'rating' => 5, 'review_count' => 15, 'stock' => 0
                ],
                (object)[
                    'id' => 4, 'name' => 'Aged Rum', 'price' => 59.99, 'original_price' => null,
                    'category_id' => 4, 'category' => (object)['name' => 'Rum'],
                    'media' => collect([(object)['url' => asset('images/placeholder.jpg')]]),
                    'status' => 'new', 'rating' => 4, 'review_count' => 20, 'stock' => 15
                ],
            ]) : $featuredProducts;
        @endphp
        @foreach ($featuredProducts as $product)
            <div class="js-slide">
                <div class="card text-center w-100">
                    <div class="position-relative">
                        <img class="card-img-top" src="{{ $product->media->first() ? $product->media->first()->url : asset('images/placeholder.jpg') }}" alt="{{ $product->name }}">
                        @if ($product->status === 'new')
                            <div class="position-absolute top-0 left-0 pt-3 pl-3">
                                <span class="badge badge-success badge-pill">New arrival</span>
                            </div>
                        @elseif ($product->status === 'sold_out')
                            <div class="position-absolute top-0 left-0 pt-3 pl-3">
                                <span class="badge badge-danger badge-pill">Sold out</span>
                            </div>
                        @endif
                        <div class="position-absolute top-0 right-0 pt-3 pr-3">
                            <button type="button" class="btn btn-sm btn-icon btn-outline-secondary rounded-circle" data-toggle="tooltip" data-placement="top" title="Save for later">
                                <span class="fas fa-heart btn-icon__inner"></span>
                            </button>
                        </div>
                    </div>

                    <div class="card-body pt-4 px-4 pb-0">
                        <div class="mb-2">
                            <a class="d-inline-block text-secondary small font-weight-medium mb-1" href="{{ route('categories.show', $product->category_id) }}">{{ $product->category->name }}</a>
                            <h3 class="font-size-1 font-weight-normal">
                                <a class="text-secondary" href="{{ route('products.show', $product->id) }}">{{ $product->name }}</a>
                            </h3>
                            <div class="d-block font-size-1">
                                <span class="font-weight-medium">${{ number_format($product->price, 2) }}</span>
                                @if ($product->original_price && $product->original_price > $product->price)
                                    <span class="text-secondary ml-1"><del>${{ number_format($product->original_price, 2) }}</del></span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card-footer border-0 pt-0 pb-4 px-4">
                        <div class="mb-3">
                            <a class="d-inline-flex align-items-center small" href="#">
                                <div class="text-warning mr-2">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <small class="{{ $i <= $product->rating ? 'fas fa-star' : 'far fa-star text-muted' }}"></small>
                                    @endfor
                                </div>
                                <span class="text-secondary">{{ $product->review_count ?? 0 }}</span>
                            </a>
                        </div>
                        
                        {{-- NEW: THREE BUTTONS --}}
                        <div class="btn-group d-flex gap-2 justify-content-center" role="group">
                            {{-- 1. Add to Cart --}}
                            <form action="{{ route('cart.store') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-sm btn-outline-primary btn-sm-wide {{ $product->stock == 0 ? 'disabled' : '' }}">
                                    <i class="fas fa-shopping-cart me-1"></i>Add to Cart
                                </button>
                            </form>
                            
                            {{-- 2. NEW: View Details --}}
                            <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-outline-info btn-sm-wide">
                                <i class="fas fa-eye me-1"></i>View Details
                            </a>
                            
                            {{-- 3. NEW: Quick Checkout --}}
                            <a href="{{ route('checkout.quick', $product->id) }}" class="btn btn-sm btn-success btn-sm-wide {{ $product->stock == 0 ? 'disabled' : '' }}">
                                <i class="fas fa-credit-card me-1"></i>${{ number_format($product->price, 2) }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="text-center u-slick__pagination mt-7 mb-0"></div>
</section>


@endsection