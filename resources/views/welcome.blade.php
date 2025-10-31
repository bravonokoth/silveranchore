@extends('layouts.app')

@section('content')
<!-- Display Success Message -->


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
        
        @foreach ($categories as $category)
            <div class="js-slide">
                <div class="category-card text-center">
                    <div class="category-image mb-3">
                        <img src="{{ $category->media()->first() ? asset('storage/' . $category->media()->first()->path) : asset('images/category-placeholder.jpg') }}" 
                             alt="{{ $category->name }}" 
                             class="w-full h-32 object-cover rounded-lg">
                    </div>
                    <div class="overlay">
                        <h3>{{ $category->name }}</h3>
                        <p class="product-count">{{ $category->products_count ?? $category->products()->count() }} Products</p>
                        <a href="{{ route('categories.show', $category->id) }}">View Products</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="text-center u-slick__pagination mt-7 mb-0"></div>
</section>

<!-- Featured Products Section -->
<section class="products max-w-7xl mx-auto py-8">
    <h2 class="text-2xl font-semibold mb-6 text-gray-800">
        Featured Products ({{ $featuredProducts->count() }})
    </h2>
    
    @if($featuredProducts->count() === 0)
        <div class="text-center py-12">
            <i class="fas fa-star text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-500 mb-2">No Featured Products</h3>
            <p class="text-gray-400">Check out our full collection!</p>
            <a href="{{ route('products.index') }}" class="mt-4 inline-block bg-blue-600 text-white px-6 py-2 rounded">Shop All</a>
        </div>
    @else
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
            
            @foreach ($featuredProducts as $product)
                <div class="js-slide">
                    <div class="card text-center w-100">
                        <div class="position-relative">
                            <img class="card-img-top" 
                                 src="{{ asset('storage/' . ($product->media->first()?->path ?? 'images/placeholder.jpg')) }}" 
                                 alt="{{ $product->name }}"
                                 style="height: 250px; object-fit: cover;">
                            
                           

                            <div class="position-absolute top-0 right-0 pt-3 pr-3">
                                <button type="button" class="btn btn-sm btn-icon btn-outline-secondary rounded-circle" 
                                        data-toggle="tooltip" data-placement="top" title="Save for later">
                                    <span class="fas fa-heart btn-icon__inner"></span>
                                </button>
                            </div>
                        </div>

                        <div class="card-body pt-4 px-4 pb-0">
                            <div class="mb-2">
                                <a class="d-inline-block text-secondary small font-weight-medium mb-1" 
                                   href="{{ route('categories.show', $product->category_id) }}">
                                    {{ $product->category?->name ?? 'Uncategorized' }}
                                </a>
                                <h3 class="font-size-1 font-weight-normal">
                                    <a class="text-secondary" href="{{ route('products.show', $product->id) }}">
                                        {{ Str::limit($product->name, 30) }}
                                    </a>
                                </h3>
                                <div class="d-block font-size-1">
                                    @if ($product->discount_price && $product->discount_price < $product->price)
                                        <span class="font-weight-medium text-danger">Ksh {{ number_format($product->discount_price, 2) }}</span>
                                        <span class="text-secondary ml-1"><del>Ksh {{ number_format($product->price, 2) }}</del></span>
                                    @else
                                        <span class="font-weight-medium">Ksh {{ number_format($product->price, 2) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

               <div class="card-footer border-0 pt-0 pb-4 px-4">
    <!-- 1. BUTTONS: Add to Cart + View Details -->
    <div class="btn-group d-flex gap-2 justify-content-center mb-3" role="group">
        <form action="{{ route('cart.store') }}" method="POST" class="d-inline">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <input type="hidden" name="quantity" value="1">
            @if($product->stock > 0)
                <button type="submit" class="btn btn-sm btn-outline-primary btn-sm-wide">
                    <i class="fas fa-shopping-cart me-1"></i>Add to Cart
                </button>
            @else
                <button type="button" class="btn btn-sm btn-outline-secondary btn-sm-wide" disabled>
                    <i class="fas fa-ban me-1"></i>Sold Out
                </button>
            @endif
        </form>

        <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-outline-info btn-sm-wide">
            <i class="fas fa-eye me-1"></i>View Details
        </a>
    </div>

    <!-- 2. BUY NOW BUTTON WITH PRICE -->
    @if($product->stock > 0)
        <a href="{{ route('checkout.quick', $product->id) }}" 
           class="btn btn-success btn-sm-wide d-block mx-auto text-center">
            <i class="fas fa-credit-card me-1"></i>
            Buy Now - 
            @if ($product->discount_price && $product->discount_price < $product->price)
                <span class="text-white">Ksh {{ number_format($product->discount_price, 2) }}</span>
                <del class="text-white-50 ms-1">Ksh {{ number_format($product->price, 2) }}</del>
            @else
                <span class="text-white">Ksh {{ number_format($product->price, 2) }}</span>
            @endif
        </a>
    @endif

    <!-- 3. RATING -->
    <div class="text-center mt-2">
        <div class="d-inline-flex align-items-center small">
            <div class="text-warning me-1">
                @for ($i = 1; $i <= 5; $i++)
                    <small class="{{ $i <= ($product->rating ?? 0) ? 'fas fa-star' : 'far fa-star text-muted' }}"></small>
                @endfor
            </div>
            <span class="text-secondary">({{ $product->review_count ?? 0 }})</span>
        </div>
    </div>
</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="text-center u-slick__pagination mt-7 mb-0"></div>
    @endif
</section>

<style>
.card-img-top { min-height: 250px; }
.js-slide { padding: 0 15px; }
.category-card { 
    text-align: center; padding: 2rem; border: 2px dashed #e5e7eb; 
    border-radius: 12px; height: 280px; display: flex; flex-direction: column; 
    justify-content: center; transition: all 0.3s; 
}
.category-card:hover { background: #f8fafc; border-color: #3b82f6; }
</style>
@endsection