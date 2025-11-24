<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Title -->
    <title>{{ config('app.name', 'The Liquor Cabinet') }}</title>

    <!-- Favicon - Using your silver.png -->
    <link rel="icon" href="{{ asset('images/silver.png') }}" type="image/png">
    <!-- Optional: Apple Touch Icon (for iOS home screen) -->
    <link rel="apple-touch-icon" href="{{ asset('images/silver.png') }}">

    <!-- Fonts & Icons -->
    <link rel="stylesheet" href="https://fonts.bunny.net/css?family=open-sans:300,400,500,600,700&display=swap" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css" />

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <!-- Vite (Tailwind + Alpine) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- jQuery & Slick Carousel -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body class="font-sans antialiased">
    <div class="flex flex-col min-h-screen bg-gray-100">
        @include('layouts.navigation')

        @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <main class="flex-grow">
            @yield('content')
        </main>

        <!-- Global Footer -->
        <section class="footer">
            <div class="footer-row">
                <div class="footer-col">
                    <h4>Info</h4>
                    <ul class="links">
                        <li><a href="{{ route('about') }}">About</a></li>
                        <li><a href="{{ route('contact') }}">Contact</a></li>
                        <li><a href="{{ route('products.index') }}">Shop</a></li>
                        <li><a href="{{ route('cart.index') }}">Cart</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Explore</h4>
                    <ul class="links">
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><a href="{{ route('products.index') }}">Featured Products</a></li>
                        <li><a href="{{ route('categories.index') }}">Categories</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Legal</h4>
                    <ul class="links">
                        <li><a href="#">Customer Agreement</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">GDPR</a></li>
                        <li><a href="#">Security</a></li>
                        <li><a href="#">Testimonials</a></li>
                        <li><a href="#">Media Kit</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Newsletter</h4>
                    <p>
                        Subscribe to our newsletter for a weekly dose
                        of news, updates, helpful tips, and
                        exclusive offers.
                    </p>
                    <form action="">
                        <input type="text" placeholder="Your email" required>
                        <button type="submit">SUBSCRIBE</button>
                    </form>
                    <div class="icons">
                        <i class="fa-brands fa-facebook-f"></i>
                        <i class="fa-brands fa-twitter"></i>
                        <i class="fa-brands fa-linkedin"></i>
                        <i class="fa-brands fa-github"></i>
                    </div>
                </div>
            </div>
            <div class="text-center py-4">
                <p>&copy; {{ date('Y') }} Silveranchore</p>
            </div>
        </section>
    </div>

    <!-- Smart Slick Carousel Initialization - Desktop Only -->
    <script>
        $(document).ready(function () {
            console.log('🎯 Global Slick: Checking device...');
            
            function initGlobalCarousels() {
                const isMobile = $(window).width() <= 768;
                console.log(`📱 Device: ${isMobile ? 'MOBILE' : 'DESKTOP'} (${$(window).width()}px)`);
                
                // Only initialize Slick on DESKTOP
                if (!isMobile) {
                    console.log('💻 Initializing global Slick for desktop...');
                    
                    $('.js-slick-carousel').each(function() {
                        const $carousel = $(this);
                        
                        // Skip if already initialized
                        if ($carousel.hasClass('slick-initialized')) {
                            console.log('⚠️ Carousel already initialized, skipping');
                            return;
                        }
                        
                        // Skip if it has page-specific initialization
                        if ($carousel.closest('.products').length || 
                            $carousel.hasClass('hero-slider') ||
                            $carousel.hasClass('u-slick--gutters-3')) {
                            console.log('⚠️ Page-specific carousel, skipping global init');
                            return;
                        }
                        
                        $carousel.slick({
                            slidesToShow: 4,
                            slidesToScroll: 3,
                            infinite: true,
                            dots: true,
                            arrows: true,
                            appendDots: '.u-slick__pagination',
                            prevArrow: '<button type="button" class="slick-prev">‹</button>',
                            nextArrow: '<button type="button" class="slick-next">›</button>',
                            responsive: [
                                { breakpoint: 992, settings: { slidesToShow: 3 } },
                                { breakpoint: 720, settings: { slidesToShow: 2 } }
                            ]
                        });
                        
                        console.log('✅ Global Slick initialized');
                    });
                } else {
                    console.log('📱 Mobile detected - Skipping global Slick initialization');
                    console.log('📱 Page-specific mobile layout will handle display');
                }
            }
            
            // Initialize on load
            initGlobalCarousels();
            
            // Reinitialize on resize (debounced)
            let resizeTimer;
            $(window).on('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    console.log('📐 Window resized, checking device type...');
                    
                    // Destroy all Slick instances
                    $('.js-slick-carousel.slick-initialized').slick('unslick');
                    
                    // Reinitialize based on screen size
                    initGlobalCarousels();
                }, 250);
            });
        });
    </script>

    <!-- Page-specific scripts -->
    @stack('scripts')
</body>
</html>