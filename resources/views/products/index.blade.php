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

<section class="products max-w-7xl mx-auto py-8 px-4">
    <h2 class="text-2xl font-semibold mb-6 text-gray-800 dark:text-gray-200">Our Products</h2>
    
    <div class="js-slick-carousel u-slick"
         data-slides-show="4"
         data-slides-scroll="3"
         data-infinite="true"
         data-center-mode="false"
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
        
      @foreach ($products as $product)
    <div class="js-slide px-2">
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
            <div class="card-footer border-0 pt-3 pb-4 px-4">
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

    <!-- Pagination -->
    <div class="text-center u-slick__pagination mt-7 mb-0"></div>
    <div class="mt-8">
        {{ $products->links() }}
    </div>
</section>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    console.log('Initializing products carousel...');
    
    // Function to initialize/destroy carousel based on screen size
    function handleProductCarousel() {
        const isMobile = window.innerWidth <= 480;
        const $carousel = $('.products .js-slick-carousel');
        
        if (isMobile) {
            // Destroy carousel on mobile and add grid class
            if ($carousel.hasClass('slick-initialized')) {
                $carousel.slick('unslick');
                console.log('Products carousel destroyed for mobile');
            }
            $carousel.addClass('mobile-grid-layout');
        } else {
            // Remove mobile grid class and initialize carousel on desktop
            $carousel.removeClass('mobile-grid-layout');
            
            if (!$carousel.hasClass('slick-initialized')) {
                const $pagination = $carousel.next('.u-slick__pagination');
                
                $carousel.slick({
                    slidesToShow: 4,
                    slidesToScroll: 3,
                    infinite: true,
                    dots: true,
                    arrows: true,
                    appendDots: $pagination.length ? $pagination : $carousel.parent(),
                    prevArrow: '<button type="button" class="slick-prev">‹</button>',
                    nextArrow: '<button type="button" class="slick-next">›</button>',
                    responsive: [
                        { 
                            breakpoint: 992, 
                            settings: { 
                                slidesToShow: 3,
                                slidesToScroll: 3
                            } 
                        },
                        { 
                            breakpoint: 720, 
                            settings: { 
                                slidesToShow: 2,
                                slidesToScroll: 2
                            } 
                        }
                    ]
                });
                console.log('Products carousel initialized for desktop');
            }
        }
    }
    
    // Run on load
    handleProductCarousel();
    
    // Run on resize (debounced)
    let resizeTimer;
    $(window).on('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(handleProductCarousel, 250);
    });
});
</script>
@endpush
