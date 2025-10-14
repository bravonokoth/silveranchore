<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'The Liquor Cabinet') }}</title>
    <link rel="stylesheet" href="https://fonts.bunny.net/css?family=open-sans:300,400,500,600,700&display=swap" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css" />
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
</head>
<body class="font-sans antialiased"> <!-- CHANGED: Removed {{ Session::has('dark_mode') && Session::get('dark_mode') ? 'dark' : '' }} to prevent dark class -->
    <div class="flex flex-col min-h-screen bg-gray-100"> <!-- CHANGED: Removed dark:bg-black to ensure light background -->
        @include('layouts.navigation')
        @isset($header)
            <header class="bg-white shadow dark:bg-zinc-900">
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
                <p>&copy; {{ date('Y') }} The Liquor Cabinet</p>
            </div>
        </section>
    </div>
    <script>
        $(document).ready(function () {
            $('.js-slick-carousel').slick({
                slidesToShow: 4,
                slidesToScroll: 3,
                infinite: true,
                dots: true,
                appendDots: '.u-slick__pagination',
                responsive: [
                    {
                        breakpoint: 992,
                        settings: { slidesToShow: 3 }
                    },
                    {
                        breakpoint: 720,
                        settings: { slidesToShow: 2 }
                    },
                    {
                        breakpoint: 480,
                        settings: { slidesToShow: 1 }
                    }
                ]
            });
        });
    </script>
</body>
</html>