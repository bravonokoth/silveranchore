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
    <!-- Filter Bar Above Products -->
    <div class="products-filter-bar">
        <div class="filter-header-row">
            <h2 class="text-2xl font-semibold mb-0 text-gray-800 dark:text-gray-200">Our Products</h2>
            
            <button id="toggleProductFilters" class="toggle-filters-mobile">
                <i class="fas fa-sliders-h"></i> Filters
            </button>
        </div>
        
        <div id="productFiltersPanel" class="filters-panel">
            <!-- Top Row Filters -->
            <div class="filter-row">
                <!-- Search -->
                <div class="filter-item">
                    <label><i class="fas fa-search"></i> Search</label>
                    <input type="text" id="productSearch" placeholder="Search products..." class="filter-input">
                </div>
                
                <!-- Category -->
                <div class="filter-item">
                    <label><i class="fas fa-tags"></i> Category</label>
                    <select id="productCategoryFilter" class="filter-select">
                        <option value="">All Categories</option>
                        @foreach($categories ?? [] as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Stock -->
                <div class="filter-item">
                    <label><i class="fas fa-box"></i> Stock</label>
                    <select id="stockFilter" class="filter-select">
                        <option value="">All</option>
                        <option value="in-stock">In Stock</option>
                        <option value="out-stock">Out of Stock</option>
                    </select>
                </div>
                
                <!-- Sort -->
                <div class="filter-item">
                    <label><i class="fas fa-sort"></i> Sort By</label>
                    <select id="productSort" class="filter-select">
                        <option value="">Default</option>
                        <option value="name-asc">Name (A-Z)</option>
                        <option value="name-desc">Name (Z-A)</option>
                        <option value="price-asc">Price (Low-High)</option>
                        <option value="price-desc">Price (High-Low)</option>
                    </select>
                </div>
                
                <!-- Reset Button -->
                <div class="filter-item">
                    <label>&nbsp;</label>
                    <button id="resetProductFilters" class="reset-btn-products">
                        <i class="fas fa-redo"></i> Reset
                    </button>
                </div>
            </div>
            
            <!-- Price Range - Below Other Filters -->
            <div class="price-range-section">
                <label><i class="fas fa-dollar-sign"></i> Price Range</label>
                <div class="price-inputs">
                    <input type="number" id="minPrice" placeholder="Min Price" class="filter-input price-input">
                    <span class="price-separator">to</span>
                    <input type="number" id="maxPrice" placeholder="Max Price" class="filter-input price-input">
                    <button id="applyPriceFilter" class="apply-price-btn">
                        <i class="fas fa-check"></i> Apply
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Results Info -->
    <div class="products-results-info">
        Showing <span id="productResultCount">{{ count($products) }}</span> of <span id="totalProductCount">{{ count($products) }}</span> products
    </div>
    
    <!-- ORIGINAL carousel structure - completely unchanged -->
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
    <div class="js-slide px-2 product-slide-item" 
         data-name="{{ strtolower($product->name) }}"
         data-category="{{ $product->category_id }}"
         data-category-name="{{ strtolower($product->category?->name ?? 'uncategorized') }}"
         data-price="{{ $product->discount_price ?? $product->price }}"
         data-stock="{{ $product->stock }}">
        <!-- ORIGINAL Product Card - 100% unchanged -->
        <div class="card text-center w-100">
            <a href="{{ route('products.show', $product->id) }}" class="position-relative">
                <img class="card-img-top" 
                     src="{{ asset('storage/' . ($product->media->first()?->path ?? 'images/placeholder.jpg')) }}" 
                     alt="{{ $product->name }}">
                
                @if ($product->stock == 0)
                    <div class="position-absolute top-0 left-0 pt-3 pl-3">
                        <span class="badge badge-danger">Sold Out</span>
                    </div>
                @else
                    <div class="position-absolute top-0 left-0 pt-3 pl-3">
                        <span class="badge badge-success">In Stock</span>
                    </div>
                @endif

                <div class="position-absolute top-0 right-0 pt-3 pr-3">
                    <button type="button" 
                            class="btn btn-sm btn-icon btn-outline-secondary rounded-circle" 
                            data-toggle="tooltip" data-placement="top" title="Save for later">
                        <span class="fas fa-heart btn-icon__inner"></span>
                    </button>
                </div>
            </a>

            <div class="card-body">
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

                <div class="product-right">
                    <div class="price-info">
                        @if ($product->discount_price && $product->discount_price < $product->price)
                            <span class="current-price">Ksh {{ number_format($product->discount_price, 0) }}</span>
                            <span class="original-price">Ksh {{ number_format($product->price, 0) }}</span>
                        @else
                            <span class="current-price">Ksh {{ number_format($product->price, 0) }}</span>
                        @endif
                    </div>

                    <div class="stock-info {{ $product->stock == 0 ? 'out-of-stock' : '' }}">
                        @if($product->stock > 0)
                            <i class="fas fa-check-circle"></i> {{ $product->stock }} in stock
                        @else
                            <i class="fas fa-times-circle"></i> Out of stock
                        @endif
                    </div>
                </div>
            </div>

            <div class="card-footer border-0 pt-3 pb-4 px-4">
                <div class="action-buttons">
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
    
    <!-- No Results Message -->
    <div id="noProductResults" class="no-results-message" style="display: none;">
        <i class="fas fa-search"></i>
        <p>No products found matching your criteria</p>
        <button id="clearProductFilters" class="clear-filters-btn">
            <i class="fas fa-times"></i> Clear Filters
        </button>
    </div>
    
    <div class="mt-8">
        {{ $products->links() }}
    </div>
</section>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    console.log('🚀 Initializing carousels...');
    
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
        
        console.log('✅ Hero slider initialized');
    }

    // Function to force mobile layout (NO SLICK)
    function forceMobileLayout($carousel) {
        const $slides = $carousel.find('.js-slide');
        
        console.log(`📱 FORCING mobile layout for ${$slides.length} slides`);
        
        // Destroy Slick if exists
        if ($carousel.hasClass('slick-initialized')) {
            $carousel.slick('unslick');
        }
        
        // Remove ALL Slick classes and styles
        $carousel.removeAttr('style')
                 .removeClass('slick-slider slick-initialized');
        
        $slides.removeAttr('style')
               .removeClass('slick-slide slick-active slick-current slick-cloned');
        
        // Remove Slick wrappers
        if ($carousel.find('.slick-list').length) {
            const $items = $carousel.find('.slick-track > .js-slide');
            if ($items.length) {
                $items.unwrap().unwrap();
            }
        }
        
        // Force visible layout
        $carousel.css({
            'display': 'flex',
            'flex-direction': 'column',
            'gap': '20px',
            'padding': '0 10px',
            'width': '100%',
            'overflow': 'visible',
            'height': 'auto'
        });
        
        // Make each slide visible
        $slides.each(function(index) {
            const $slide = $(this);
            $slide.css({
                'display': 'block',
                'width': '100%',
                'max-width': '100%',
                'float': 'none',
                'margin': '0',
                'padding': '0',
                'position': 'static',
                'transform': 'none',
                'opacity': '1',
                'visibility': 'visible',
                'left': 'auto',
                'right': 'auto',
                'top': 'auto',
                'height': 'auto'
            });
            
            // Make card visible
            $slide.find('.card, .category-card').css({
                'display': 'flex',
                'flex-direction': 'column',
                'width': '100%',
                'max-width': '100%',
                'opacity': '1',
                'visibility': 'visible',
                'position': 'relative',
                'transform': 'none',
                'height': 'auto'
            });
            
            console.log(`  ✓ Slide ${index + 1} forced visible`);
        });
        
        // Hide pagination
        $carousel.next('.u-slick__pagination').hide();
        
        console.log(`✅ Mobile layout applied - ${$slides.length} slides visible`);
    }

    // Initialize category and product carousels
    function initCarousels() {
        const isMobile = $(window).width() <= 768;
        const windowWidth = $(window).width();
        
        console.log(`📐 Window: ${windowWidth}px | Mobile: ${isMobile}`);
        
        $('.u-slick--gutters-3').each(function(index) {
            const $carousel = $(this);
            const $slides = $carousel.find('.js-slide');
            const $pagination = $carousel.next('.u-slick__pagination');
            
            console.log(`\n📦 Carousel ${index + 1}:`);
            console.log(`  - Slides found: ${$slides.length}`);
            console.log(`  - Mode: ${isMobile ? 'MOBILE' : 'DESKTOP'}`);
            
            if (isMobile) {
                // FORCE mobile layout - NO SLICK
                forceMobileLayout($carousel);
                
            } else {
                // Desktop: Use Slick carousel
                console.log(`  💻 Initializing Slick carousel...`);
                
                // Destroy existing Slick
                if ($carousel.hasClass('slick-initialized')) {
                    $carousel.slick('unslick');
                }
                
                // Clear mobile styles
                $carousel.removeAttr('style');
                $slides.removeAttr('style');
                $slides.find('.card, .category-card').removeAttr('style');
                
                // Initialize Slick
                $carousel.slick({
                    slidesToShow: 4,
                    slidesToScroll: 1,
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
                                slidesToScroll: 1
                            } 
                        },
                        { 
                            breakpoint: 720, 
                            settings: { 
                                slidesToShow: 2,
                                slidesToScroll: 1
                            } 
                        }
                    ]
                });
                
                // Show pagination
                if ($pagination.length) {
                    $pagination.show();
                }
                
                console.log(`  ✅ Slick initialized`);
            }
        });
        
        console.log('\n🎉 All carousels initialized!\n');
    }
    
    // Initialize on load
    initCarousels();
    
    // Reinitialize on resize (debounced)
    let resizeTimer;
    $(window).on('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            console.log('\n📐 RESIZE detected - Reinitializing...');
            initCarousels();
        }, 250);
    });
});
</script>
@endpush

<style>
/* Products Filter Bar */
.products-filter-bar {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.filter-header-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.toggle-filters-mobile {
    display: none;
    padding: 10px 18px;
    background: var(--ice-blue);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    gap: 8px;
    align-items: center;
    transition: all 0.3s ease;
}

.toggle-filters-mobile:hover {
    background: var(--ice-accent);
}

.filters-panel {
    display: block;
}

.filter-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    align-items: end;
    margin-bottom: 15px;
}

.filter-item {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.filter-item label {
    font-size: 13px;
    font-weight: 600;
    color: #475569;
    display: flex;
    align-items: center;
    gap: 6px;
}

.filter-item label i {
    color: var(--ice-blue);
    font-size: 12px;
}

.filter-input,
.filter-select {
    padding: 10px 12px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
    background: white;
}

.filter-input:focus,
.filter-select:focus {
    outline: none;
    border-color: var(--ice-blue);
    box-shadow: 0 0 0 3px var(--ice-glow);
}

/* Price Range Section - Full Width Below */
.price-range-section {
    padding-top: 15px;
    border-top: 2px solid #f1f5f9;
    margin-top: 5px;
}

.price-range-section > label {
    font-size: 13px;
    font-weight: 600;
    color: #475569;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 10px;
}

.price-range-section > label i {
    color: var(--ice-blue);
    font-size: 12px;
}

.price-inputs {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.price-input {
    flex: 1;
    min-width: 120px;
    padding: 10px 12px;
}

.price-separator {
    color: #94a3b8;
    font-weight: 600;
    font-size: 14px;
}

.apply-price-btn {
    padding: 10px 20px;
    background: var(--ice-blue);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
}

.apply-price-btn:hover {
    background: var(--ice-accent);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px var(--ice-glow);
}

.reset-btn-products {
    width: 100%;
    padding: 10px 12px;
    background: white;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    color: #64748b;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.reset-btn-products:hover {
    background: #f8fafc;
    border-color: var(--ice-blue);
    color: var(--ice-blue);
}

.products-results-info {
    font-size: 14px;
    color: #64748b;
    margin-bottom: 15px;
    padding: 0 5px;
}

.products-results-info span {
    font-weight: 600;
    color: var(--ice-blue);
}

.no-results-message {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 12px;
    margin: 20px 0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.no-results-message i {
    font-size: 48px;
    color: #cbd5e1;
    margin-bottom: 15px;
}

.no-results-message p {
    font-size: 16px;
    color: #64748b;
    margin-bottom: 20px;
}

.clear-filters-btn {
    padding: 10px 24px;
    background: var(--ice-blue);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.clear-filters-btn:hover {
    background: var(--ice-accent);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px var(--ice-glow);
}

/* Dark Mode */
body.dark .products-filter-bar,
body.dark .no-results-message {
    background: #2d3748;
}

body.dark .filter-input,
body.dark .filter-select,
body.dark .reset-btn-products {
    background: #1a202c;
    border-color: #4a5568;
    color: #e2e8f0;
}

body.dark .filter-item label,
body.dark .price-range-section > label {
    color: #cbd5e1;
}

body.dark .products-results-info {
    color: #94a3b8;
}

body.dark .price-range-section {
    border-top-color: #4a5568;
}

/* Responsive */
@media (max-width: 768px) {
    .toggle-filters-mobile {
        display: flex;
    }
    
    .filters-panel {
        display: none;
        margin-top: 15px;
    }
    
    .filter-row {
        grid-template-columns: 1fr;
        gap: 12px;
    }
    
    .price-inputs {
        flex-direction: column;
        align-items: stretch;
    }
    
    .price-input {
        width: 100%;
    }
    
    .price-separator {
        display: none;
    }
    
    .apply-price-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>