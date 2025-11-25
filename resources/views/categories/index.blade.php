@extends('layouts.app')

@section('content')
<!-- Category Section -->
<section class="max-w-7xl mx-auto py-8 px-4">
    <!-- Filter Bar - Above existing carousel -->
    <div class="category-filter-bar">
        <h2 class="text-2xl font-semibold mb-0 text-gray-800 dark:text-gray-200">Our Categories</h2>
        
        <div class="filter-controls">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="categorySearch" placeholder="Search categories...">
            </div>
            
            <select id="categorySort" class="sort-select">
                <option value="">Sort By</option>
                <option value="name-asc">Name (A-Z)</option>
                <option value="name-desc">Name (Z-A)</option>
                <option value="products-desc">Most Products</option>
                <option value="products-asc">Least Products</option>
            </select>
            
            <button id="resetCategoryFilters" class="reset-btn">
                <i class="fas fa-redo"></i> Reset
            </button>
        </div>
    </div>
    
    <div class="category-results-info">
        <span id="categoryResultCount">{{ count($categories) }}</span> categories found
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
        
        @foreach ($categories as $category)
            <div class="js-slide px-2 category-slide-item" 
                 data-name="{{ strtolower($category->name) }}" 
                 data-products="{{ $category->products_count ?? $category->products()->count() }}">
                <div class="category-card">
                    <div class="category-image">
                        <img src="{{ asset('storage/' . ($category->media->first()?->path ?? 'images/category-placeholder.jpg')) }}" 
                             alt="{{ $category->name }}" 
                             onerror="this.src='{{ asset('storage/images/category-placeholder.png') }}';">
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
    
    <!-- No Results Message (hidden by default) -->
    <div id="noCategoryResults" class="no-results-message" style="display: none;">
        <i class="fas fa-search"></i>
        <p>No categories found matching your criteria</p>
    </div>
</section>

<x-features-section />
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let categoryCarousel;
    
    // Initialize carousel ONCE
    function initCategoryCarousel() {
        const $carousel = $('.js-slick-carousel');
        const $pagination = $('.u-slick__pagination');
        
        if (!$carousel.hasClass('slick-initialized')) {
            categoryCarousel = $carousel.slick({
                slidesToShow: 4,
                slidesToScroll: 3,
                infinite: true,
                dots: true,
                arrows: true,
                appendDots: $pagination,
                prevArrow: '<button type="button" class="slick-prev">‹</button>',
                nextArrow: '<button type="button" class="slick-next">›</button>',
                responsive: [
                    { breakpoint: 992, settings: { slidesToShow: 3, slidesToScroll: 3 } },
                    { breakpoint: 720, settings: { slidesToShow: 2, slidesToScroll: 2 } },
                    { breakpoint: 480, settings: { slidesToShow: 1, slidesToScroll: 1 } }
                ]
            });
        }
    }
    
    // Filter categories
    function filterCategories() {
        const searchTerm = $('#categorySearch').val().toLowerCase();
        const sortBy = $('#categorySort').val();
        
        let items = $('.category-slide-item');
        let visibleCount = 0;
        
        // Show/hide based on search
        items.each(function() {
            const name = $(this).data('name');
            if (!searchTerm || name.includes(searchTerm)) {
                $(this).show();
                visibleCount++;
            } else {
                $(this).hide();
            }
        });
        
        // Sort if needed
        if (sortBy) {
            let visibleItems = items.filter(':visible').get();
            
            visibleItems.sort(function(a, b) {
                const aName = $(a).data('name');
                const bName = $(b).data('name');
                const aProducts = parseInt($(a).data('products'));
                const bProducts = parseInt($(b).data('products'));
                
                switch(sortBy) {
                    case 'name-asc': return aName.localeCompare(bName);
                    case 'name-desc': return bName.localeCompare(aName);
                    case 'products-desc': return bProducts - aProducts;
                    case 'products-asc': return aProducts - bProducts;
                    default: return 0;
                }
            });
            
            // Reorder slides
            const $carousel = $('.js-slick-carousel');
            $(visibleItems).each(function() {
                $carousel.slick('slickAdd', $(this));
            });
        }
        
        // Update count
        $('#categoryResultCount').text(visibleCount);
        
        // Show/hide no results
        if (visibleCount === 0) {
            $('.js-slick-carousel, .u-slick__pagination').hide();
            $('#noCategoryResults').show();
        } else {
            $('.js-slick-carousel, .u-slick__pagination').show();
            $('#noCategoryResults').hide();
        }
        
        // Refresh carousel
        if ($('.js-slick-carousel').hasClass('slick-initialized')) {
            $('.js-slick-carousel').slick('setPosition');
        }
    }
    
    // Event listeners
    $('#categorySearch').on('keyup', filterCategories);
    $('#categorySort').on('change', filterCategories);
    
    $('#resetCategoryFilters').on('click', function() {
        $('#categorySearch').val('');
        $('#categorySort').val('');
        filterCategories();
    });
    
    // Initialize carousel on load
    initCategoryCarousel();
});
</script>
@endpush

<style>
/* Filter Bar Styling */
.category-filter-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding: 20px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.filter-controls {
    display: flex;
    gap: 12px;
    align-items: center;
}

.search-box {
    position: relative;
    display: flex;
    align-items: center;
}

.search-box i {
    position: absolute;
    left: 12px;
    color: #94a3b8;
    pointer-events: none;
}

.search-box input {
    padding: 10px 14px 10px 38px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    width: 240px;
    transition: all 0.3s ease;
}

.search-box input:focus {
    outline: none;
    border-color: var(--ice-blue);
    box-shadow: 0 0 0 3px var(--ice-glow);
}

.sort-select {
    padding: 10px 14px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease;
}

.sort-select:focus {
    outline: none;
    border-color: var(--ice-blue);
    box-shadow: 0 0 0 3px var(--ice-glow);
}

.reset-btn {
    padding: 10px 18px;
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
    gap: 6px;
}

.reset-btn:hover {
    background: #f8fafc;
    border-color: var(--ice-blue);
    color: var(--ice-blue);
}

.category-results-info {
    font-size: 14px;
    color: #64748b;
    margin-bottom: 15px;
    padding: 0 5px;
}

.category-results-info span {
    font-weight: 600;
    color: var(--ice-blue);
}

.no-results-message {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 12px;
    margin-top: 20px;
}

.no-results-message i {
    font-size: 48px;
    color: #cbd5e1;
    margin-bottom: 15px;
}

.no-results-message p {
    font-size: 16px;
    color: #64748b;
}

/* Dark Mode */
body.dark .category-filter-bar {
    background: #2d3748;
}

body.dark .search-box input,
body.dark .sort-select,
body.dark .reset-btn {
    background: #1a202c;
    border-color: #4a5568;
    color: #e2e8f0;
}

body.dark .no-results-message {
    background: #2d3748;
}

/* Responsive */
@media (max-width: 768px) {
    .category-filter-bar {
        flex-direction: column;
        align-items: stretch;
        gap: 15px;
    }
    
    .filter-controls {
        flex-wrap: wrap;
    }
    
    .search-box input {
        width: 100%;
        flex: 1;
    }
    
    .sort-select,
    .reset-btn {
        flex: 1;
    }
}

@media (max-width: 719px) {
    .js-slick-carousel[data-slides-show="4"] {
        /* Override Slick's default 1-slide-on-mobile behavior */
        --slick-slides-to-show: 2 !important;
    }
    
    .js-slick-carousel .slick-track {
        display: grid !important;
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 16px;
    }
    
    .js-slick-carousel .slick-slide {
        float: none !important;
        width: auto !important;
        margin: 0 !important;
    }
    
    .js-slick-carousel .slick-list {
        padding: 0 8px !important;
    }
}

/* Extra small phones – still 2 cards, tighter gap */
@media (max-width: 480px) {
    .js-slick-carousel .slick-track {
        gap: 12px;
    }
    .js-slick-carousel .slick-list {
        padding: 0 6px !important;
    }
}
</style>