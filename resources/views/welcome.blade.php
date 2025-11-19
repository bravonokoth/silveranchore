@extends('layouts.app')

@section('content')
<!-- Display Success Message -->


<!-- HERO SECTION - FULL WIDTH & FULL HEIGHT -->
<section class="hero-section position-relative overflow-hidden">
    <!-- Full-width background container -->
    <div class="hero-background w-100 h-100 position-absolute top-0 start-0">
        <!-- Slick Slider (fills 100% of hero-background) -->
        <div class="js-slick-carousel hero-slider w-100 h-100"
             data-slides-show="1"
             data-fade="true"
             data-autoplay="true"
             data-autoplay-speed="5000"
             data-infinite="true"
             data-pagi-classes="hero-pagination position-absolute bottom-0 start-50 translate-middle-x mb-4">

            @php
                $activeBanners = \App\Models\Banner::where('is_active', true)
                                                    ->orderBy('created_at', 'desc')
                                                    ->get();
            @endphp

            @forelse($activeBanners as $banner)
                <div class="hero-slide w-100 h-100">
                    @if($banner->link)
                        <a href="{{ $banner->link }}" class="d-block w-100 h-100">
                            <img src="{{ asset('storage/' . $banner->image_path) }}" 
                                 alt="{{ $banner->title }}" 
                                 class="hero-image w-100 h-100 object-cover">
                        </a>
                    @else
                        <img src="{{ asset('storage/' . $banner->image_path) }}" 
                             alt="{{ $banner->title }}" 
                             class="hero-image w-100 h-100 object-cover">
                    @endif
                </div>
                   @empty
                <div class="hero-slide w-100 h-100">
                    <img src="{{ asset('images/pexels.jpg') }}" 
                         alt="The Liquor Cabinet" 
                         class="hero-image w-100 h-100 object-cover">
                </div>
            @endforelse
        </div>

        <!-- Fallback background (only shows if JS fails) -->
        <div class="fallback-bg w-100 h-100 position-absolute top-0 start-0"
             style="background: url('{{ asset('images/pexels.jpg') }}') center/cover no-repeat; filter: brightness(0.6);">
        </div>
    </div>

    <!-- Content Overlay -->
    <div class="hero-content position-relative z-10 text-center">
        <h1 class="text-4xl font-bold text-white">The Liquor Cabinet</h1>
        <p class="text-lg text-white mb-6">Discover our premium selection of fine liquors and accessories.</p>
        <div class="cta-buttons flex gap-4 justify-center">
            <a href="{{ route('products.index') }}" class="cta-button">Shop Now</a>
            <a href="{{ route('categories.index') }}" class="cta-button secondary">Browse Categories</a>
        </div>
    </div>

    <!-- Slick Dots Pagination -->
    <div class="hero-pagination position-absolute bottom-0 start-50 translate-middle-x mb-4 z-10"></div>
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
        <!-- Product Card -->
        <div class="card text-center w-100">
            <!-- Product Image - Clickable to Product Details -->
            <a href="{{ route('products.show', $product->id) }}" class="position-relative">
                <img class="card-img-top" 
                     src="{{ asset('storage/' . ($product->media->first()?->path ?? 'images/placeholder.jpg')) }}" 
                     alt="{{ $product->name }}">
                
                <!-- Stock Badge -->
                @if ($product->stock == 0)
                    <div class="position-absolute top-0 left-0 pt-3 pl-3">
                        <span class="badge badge-danger">Sold Out</span>
                    </div>
                @else
                    <div class="position-absolute top-0 left-0 pt-3 pl-3">
                        <span class="badge badge-success">In Stock</span>
                    </div>
                @endif

                <!-- Wishlist Heart -->
                <div class="position-absolute top-0 right-0 pt-3 pr-3">
                    <button type="button" 
                            class="btn btn-sm btn-icon btn-outline-secondary rounded-circle" 
                            data-toggle="tooltip" data-placement="top" title="Save for later">
                        <span class="fas fa-heart btn-icon__inner"></span>
                    </button>
                </div>
            </a>

          <!-- Product Info - Reversed Split Layout -->
            <div class="card-body">
                <!-- LEFT SIDE: Product Name & Category -->
                <div class="product-left">
                    <h3>
                        <a href="{{ route('products.show', $product->id) }}">
                            {{ $product->name }}
                        </a>
                    </h3>
                    <a href="{{ route('categories.show', $product->category_id) }}" class="product-category">
                        {{ $product->category?->name ?? 'Uncategorized' }}
                    </a>
                </div>

                <!-- RIGHT SIDE: Price & Stock -->
                <div class="product-right">
                    <div class="price-info">
                        @if ($product->discount_price && $product->discount_price < $product->price)
                            <span class="current-price">Ksh {{ number_format($product->discount_price, 0) }}</span>
                            <span class="original-price">Ksh {{ number_format($product->price, 0) }}</span>
                        @else
                            <span class="current-price">Ksh {{ number_format($product->price, 0) }}</span>
                        @endif
                    </div>

                    <!-- Stock Info -->
                    <div class="stock-info {{ $product->stock == 0 ? 'out-of-stock' : '' }}">
                        @if($product->stock > 0)
                            <i class="fas fa-check-circle"></i> {{ $product->stock }} in stock
                        @else
                            <i class="fas fa-times-circle"></i> Out of stock
                        @endif
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card-footer border-0">
                <div class="action-buttons">
                    <!-- Add to Cart Button -->
                    <form action="{{ route('cart.store') }}" method="POST" style="flex: 1;">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        @if($product->stock > 0)
                            <button type="submit" class="add-to-cart-btn">
                                <i class="fas fa-shopping-cart"></i>Add to Cart
                            </button>
                        @else
                            <button type="button" class="add-to-cart-btn btn-disabled" disabled>
                                <i class="fas fa-ban"></i>Sold Out
                            </button>
                        @endif
                    </form>

                    <!-- Buy Now Button -->
                    @if($product->stock > 0)
                        <a href="{{ route('checkout.quick', $product->id) }}" class="buy-now-btn">
                            <i class="fas fa-bolt"></i>Buy Now
                        </a>
                    @endif
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

@push('scripts')
<script>
$(document).ready(function() {
    console.log('Initializing sliders...');
    
    // Initialize hero slider (always carousel)
    const $heroSlider = $('.js-slick-carousel.hero-slider');
    
    if ($heroSlider.length) {
        $heroSlider.slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            fade: true,
            autoplay: true,
            autoplaySpeed: 5000,
            infinite: true,
            arrows: true,
            dots: true,
            cssEase: 'ease-in-out',
            speed: 1000,
            pauseOnHover: false,
            pauseOnFocus: false,
            adaptiveHeight: false,
            appendDots: $('.hero-pagination'),
            prevArrow: '<button type="button" class="slick-prev">←</button>',
            nextArrow: '<button type="button" class="slick-next">→</button>'
        });
        
        console.log('Hero slider initialized');
    }

    // Initialize category and product carousels
    $('.u-slick--gutters-3').each(function(index) {
        const $this = $(this);
        const $pagination = $this.next('.u-slick__pagination');
        
        $this.slick({
            slidesToShow: 4,
            slidesToScroll: 1, // Changed to 1 for smoother scrolling
            infinite: true,
            dots: true,
            arrows: true,
            appendDots: $pagination.length ? $pagination : $this.parent(),
            prevArrow: '<button type="button" class="slick-prev">‹</button>',
            nextArrow: '<button type="button" class="slick-next">›</button>',
            responsive: [
                { 
                    breakpoint: 992, 
                    settings: { 
                        slidesToShow: 3,
                        slidesToScroll: 1
                    } 
                },
                { 
                    breakpoint: 720, 
                    settings: { 
                        slidesToShow: 2,
                        slidesToScroll: 1
                    } 
                },
                { 
                    breakpoint: 480, 
                    settings: { 
                        slidesToShow: 2,  // Show 2 items on mobile
                        slidesToScroll: 1, // Scroll 1 at a time
                        arrows: true,      // Keep arrows
                        dots: false,        // Keep dots
                        centerMode: false,  // Disable center mode
                        variableWidth: false // Disable variable width
                    } 
                },
                { 
                    breakpoint: 360, 
                    settings: { 
                        slidesToShow: 1,  // Show 1 on very small screens
                        slidesToScroll: 1,
                        arrows: true,
                        dots: false
                    } 
                }
            ]
        });
        
        console.log('Carousel ' + (index + 1) + ' initialized');
    });
});
</script>
@endpush
