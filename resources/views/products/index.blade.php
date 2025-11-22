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
    console.log('Initializing products with filters...');
    
    let productCarousel;
    let allProducts = $('.product-slide-item');
    let totalProducts = allProducts.length;
    
    // Initialize carousel
    function initProductCarousel() {
        const isMobile = window.innerWidth <= 480;
        const $carousel = $('.products .js-slick-carousel');
        
        if (isMobile) {
            if ($carousel.hasClass('slick-initialized')) {
                $carousel.slick('unslick');
                console.log('Products carousel destroyed for mobile');
            }
            $carousel.addClass('mobile-grid-layout');
        } else {
            $carousel.removeClass('mobile-grid-layout');
            
            if (!$carousel.hasClass('slick-initialized')) {
                const $pagination = $carousel.next('.u-slick__pagination');
                
                productCarousel = $carousel.slick({
                    slidesToShow: 4,
                    slidesToScroll: 3,
                    infinite: true,
                    dots: true,
                    arrows: true,
                    appendDots: $pagination.length ? $pagination : $carousel.parent(),
                    prevArrow: '<button type="button" class="slick-prev">‹</button>',
                    nextArrow: '<button type="button" class="slick-next">›</button>',
                    responsive: [
                        { breakpoint: 992, settings: { slidesToShow: 3, slidesToScroll: 3 } },
                        { breakpoint: 720, settings: { slidesToShow: 2, slidesToScroll: 2 } }
                    ]
                });
                console.log('Products carousel initialized for desktop');
            }
        }
    }
    
    // Toggle filters on mobile
    $('#toggleProductFilters').on('click', function() {
        $('#productFiltersPanel').slideToggle(300);
        const $icon = $(this).find('i');
        $icon.toggleClass('fa-sliders-h fa-times');
    });
    
    // Filter products
    function filterProducts() {
        const searchTerm = $('#productSearch').val().toLowerCase().trim();
        const categoryId = $('#productCategoryFilter').val();
        const minPrice = parseFloat($('#minPrice').val()) || 0;
        const maxPrice = parseFloat($('#maxPrice').val()) || Infinity;
        const stockFilter = $('#stockFilter').val();
        const sortBy = $('#productSort').val();
        
        console.log('Filtering:', { searchTerm, categoryId, minPrice, maxPrice, stockFilter, sortBy });
        
        let visibleCount = 0;
        
        // Filter items
        allProducts.each(function() {
            let show = true;
            const $item = $(this);
            const name = $item.data('name') || '';
            const category = $item.data('category') ? $item.data('category').toString() : '';
            const price = parseFloat($item.data('price')) || 0;
            const stock = parseInt($item.data('stock')) || 0;
            
            // Search filter
            if (searchTerm && !name.includes(searchTerm)) {
                show = false;
            }
            
            // Category filter
            if (categoryId && category !== categoryId) {
                show = false;
            }
            
            // Price filter
            if (price < minPrice || price > maxPrice) {
                show = false;
            }
            
            // Stock filter
            if (stockFilter === 'in-stock' && stock <= 0) {
                show = false;
            } else if (stockFilter === 'out-stock' && stock > 0) {
                show = false;
            }
            
            // Show/hide the slide
            if (show) {
                $item.show();
                visibleCount++;
            } else {
                $item.hide();
            }
        });
        
        console.log('Visible products:', visibleCount);
        
        // Sort visible items
        if (sortBy) {
            let visibleItems = allProducts.filter(':visible').get();
            
            visibleItems.sort(function(a, b) {
                const $a = $(a);
                const $b = $(b);
                
                switch(sortBy) {
                    case 'name-asc':
                        return ($a.data('name') || '').localeCompare($b.data('name') || '');
                    case 'name-desc':
                        return ($b.data('name') || '').localeCompare($a.data('name') || '');
                    case 'price-asc':
                        return (parseFloat($a.data('price')) || 0) - (parseFloat($b.data('price')) || 0);
                    case 'price-desc':
                        return (parseFloat($b.data('price')) || 0) - (parseFloat($a.data('price')) || 0);
                    default:
                        return 0;
                }
            });
            
            // Reorder in DOM
            const $carousel = $('.products .js-slick-carousel');
            $(visibleItems).each(function() {
                $carousel.append(this);
            });
        }
        
        // Update count
        $('#productResultCount').text(visibleCount);
        
        // Show/hide no results
        const $carousel = $('.js-slick-carousel');
        const $pagination = $('.u-slick__pagination');
        
        if (visibleCount === 0) {
            $carousel.hide();
            $pagination.hide();
            $('#noProductResults').show();
        } else {
            $carousel.show();
            $pagination.show();
            $('#noProductResults').hide();
        }
        
        // Refresh carousel if initialized
        if ($carousel.hasClass('slick-initialized')) {
            $carousel.slick('setPosition');
        }
    }
    
    // Event listeners
    $('#productSearch').on('keyup', filterProducts);
    $('#productCategoryFilter, #stockFilter, #productSort').on('change', filterProducts);
    $('#applyPriceFilter').on('click', filterProducts);
    $('#minPrice, #maxPrice').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            filterProducts();
        }
    });
    
    // Reset filters
    function resetFilters() {
        $('#productSearch').val('');
        $('#productCategoryFilter').val('');
        $('#minPrice').val('');
        $('#maxPrice').val('');
        $('#stockFilter').val('');
        $('#productSort').val('');
        filterProducts();
    }
    
    $('#resetProductFilters, #clearProductFilters').on('click', resetFilters);
    
    // Handle resize
    let resizeTimer;
    $(window).on('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(initProductCarousel, 250);
    });
    
    // Initialize
    initProductCarousel();
    
    console.log('Products filter system ready');
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