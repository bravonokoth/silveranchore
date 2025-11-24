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

<section class="max-w-7xl mx-auto py-16 px-4">
   <h2 class="text-3xl md:text-4xl font-extrabold text-ice-blue tracking-tight mb-12 relative inline-block">
        Shop by Category
        <span class="block w-36 h-1.5 bg-gradient-to-r from-ice-blue to-transparent rounded-full mt-3"></span>
    </h2>

    <div id="categories-grid">
        @foreach ($categories as $category)
            <a href="{{ route('categories.show', $category->id) }}" class="block">
                <div class="category-card">
                    <img src="{{ $category->media()->first() 
                        ? asset('storage/' . $category->media()->first()->path) 
                        : asset('images/category-placeholder.jpg') }}" 
                         alt="{{ $category->name }}">

                    <div class="overlay">
                        <h3>{{ $category->name }}</h3>
                        <div class="product-count">
                            {{ $category->products_count ?? $category->products()->count() }} Products
                        </div>
                        <span class="view-btn">View Collection</span>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</section>

<!-- PRODUCT SECTIONS – REDUCED SPACING + FIXED ARROW + VISIBLE GLOW -->
<section class="max-w-7xl mx-auto py-8 px-4"> <!-- Reduced from py-16 to py-8 -->

    <!-- MOST POPULAR -->
    <div class="mb-20">
        <div class="flex justify-between items-center mb-12 relative">
            <h3 class="text-3xl md:text-4xl font-extrabold text-ice-blue tracking-tight relative z-10">
                Most Popular
                <span class="block w-20 h-1.5 bg-gradient-to-r from-ice-blue to-transparent rounded-full mt-3"></span>
            </h3>
            <a href="{{ route('products.index') }}?sort=popular"
               class="text-blue-600 hover:text-blue-800 font-semibold text-lg flex items-center gap-2 group">
                View All
                <svg class="w-5 h-5 transition-transform group-hover:translate-x-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-7" id="popular-section" data-type="popular" data-page="1">
            @php
                $initialPopular = \App\Models\Product::where('is_active', true)
                    ->select('products.*')
                    ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
                    ->groupBy('products.id')
                    ->orderByRaw('COUNT(order_items.id) DESC, products.created_at DESC')
                    ->with(['media', 'category'])
                    ->take(8)
                    ->get();
            @endphp
            @foreach($initialPopular as $product)
                @include('partials.product-card', compact('product'))
            @endforeach
        </div>

        <div class="text-center mt-16">
            <div class="loading-spinner hidden inline-flex items-center gap-3 text-blue-600 font-medium text-lg">
                Loading more products...
                <svg class="w-6 h-6 animate-spin" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" class="opacity-25"/>
                    <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- TRENDING NOW -->
    <div class="mb-20">
        <div class="flex justify-between items-center mb-12 relative">
            <h3 class="text-3xl md:text-4xl font-extrabold text-ice-blue tracking-tight relative z-10">
                Trending Now
                <span class="block w-20 h-1.5 bg-gradient-to-r from-ice-blue to-transparent rounded-full mt-3"></span>
            </h3>
            <a href="{{ route('products.index') }}?sort=trending"
               class="text-blue-600 hover:text-blue-800 font-semibold text-lg flex items-center gap-2 group">
                View All
                <svg class="w-5 h-5 transition-transform group-hover:translate-x-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-7" id="trending-section" data-type="trending" data-page="1">
            @php
                $initialTrending = \App\Models\Product::where('is_active', true)
                    ->where('created_at', '>=', now()->subDays(30))
                    ->with(['media', 'category'])
                    ->latest()
                    ->take(8)
                    ->get();
            @endphp
            @foreach($initialTrending as $product)
                @include('partials.product-card', compact('product'))
            @endforeach
        </div>
        <!-- spinner same as above -->
    </div>

    <!-- NEW ARRIVALS -->
    <div class="mb-20">
        <div class="flex justify-between items-center mb-12 relative">
            <h3 class="text-3xl md:text-4xl font-extrabold text-ice-blue tracking-tight relative z-10">
                New Arrivals
                <span class="block w-20 h-1.5 bg-gradient-to-r from-ice-blue to-transparent rounded-full mt-3"></span>
            </h3>
            <a href="{{ route('products.index') }}?sort=newest"
               class="text-blue-600 hover:text-blue-800 font-semibold text-lg flex items-center gap-2 group">
                View All
                <svg class="w-5 h-5 transition-transform group-hover:translate-x-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-7" id="new-section" data-type="new" data-page="1">
            @php
                $initialNew = \App\Models\Product::where('is_active', true)
                    ->with(['media', 'category'])
                    ->latest()
                    ->take(8)
                    ->get();
            @endphp
            @foreach($initialNew as $product)
                @include('partials.product-card', compact('product'))
            @endforeach
        </div>
        <!-- spinner same as above -->
    </div>
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

.text-ice-blue {
    color: #60a5fa !important;
}

/* Optional: Add a subtle underline glow effect */
h3.text-ice-blue {
    position: relative;
}

h3.text-ice-blue::after {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 0;
    width: 60px;
    height: 4px;
    background: linear-gradient(90deg, #60a5fa, transparent);
    border-radius: 2px;
    opacity: 0.7;
}

/* Increase gap between cards */
.grid.gap-7 {
    gap: 1.75rem;
}

/* Mobile spacing tweak */
@media (max-width: 640px) {
    .grid.gap-7 {
        gap: 1.5rem;
    }
}

</style>





@endsection

@push('scripts')



<script>

document.addEventListener('DOMContentLoaded', function () {
    const sections = document.querySelectorAll('#popular-section, #trending-section, #new-section');

    sections.forEach(section => {
        let isLoading = false;

        const loadMore = () => {
            if (isLoading) return;
            isLoading = true;

            const page = parseInt(section.dataset.page) + 1;
            section.dataset.page = page;

            const spinner = section.closest('.mb-20').querySelector('.loading-spinner');
            if (spinner) spinner.classList.remove('hidden');

            fetch(`/load-more?type=${section.dataset.type}&page=${page}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.html && data.html.trim()) {
                    section.insertAdjacentHTML('beforeend', data.html);
                }
                if (!data.hasMore) {
                    if (spinner) spinner.remove();
                } else if (spinner) {
                    spinner.classList.add('hidden');
                }
                isLoading = false;
            })
            .catch(err => {
                console.error('Load more failed', err);
                if (spinner) spinner.textContent = 'Failed to load';
                isLoading = false;
            });
        };

        // Create sentinel at bottom of each section
        const sentinel = document.createElement('div');
        sentinel.style.height = '20px';
        sentinel.className = 'load-more-sentinel';
        section.appendChild(sentinel);

        const observer = new IntersectionObserver(entries => {
            if (entries[0].isIntersecting && !isLoading) {
                loadMore();
            }
        }, { 
            rootMargin: '0px 0px 400px 0px',
            threshold: 0.1
        });

        observer.observe(sentinel);
    });
});
</script>


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
