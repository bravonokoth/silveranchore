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

    <!-- Styles -->
<style>
/* Overlay */
.cart-sidebar-overlay {
    position: fixed;
    top: 0;
    right: -100%;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    transition: right 0.3s ease-in-out;
    backdrop-filter: blur(2px);
}

.cart-sidebar-overlay.active {
    right: 0;
}

/* Sidebar */
.cart-sidebar {
    position: absolute;
    top: 0;
    right: 0;
    width: 100%;
    max-width: 450px;
    height: 100%;
    background: #fff;
    box-shadow: -4px 0 20px rgba(0, 0, 0, 0.15);
    display: flex;
    flex-direction: column;
    transform: translateX(100%);
    transition: transform 0.3s ease-in-out;
}

.cart-sidebar-overlay.active .cart-sidebar {
    transform: translateX(0);
}

/* Header */
.cart-sidebar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
}

.cart-sidebar-header h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 700;
    color: #111827;
}

.cart-close-btn {
    background: none;
    border: none;
    cursor: pointer;
    padding: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.375rem;
    transition: background 0.2s;
}

.cart-close-btn:hover {
    background: #e5e7eb;
}

.cart-close-btn svg {
    stroke: #6b7280;
}

/* Content */
.cart-sidebar-content {
    flex: 1;
    overflow-y: auto;
    padding: 1rem;
}

.cart-loading {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 3rem 1rem;
    color: #6b7280;
}

.spinner {
    width: 40px;
    height: 40px;
    border: 3px solid #e5e7eb;
    border-top-color: #3b82f6;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
    margin-bottom: 1rem;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.cart-empty {
    text-align: center;
    padding: 3rem 1rem;
    color: #6b7280;
}

.cart-empty svg {
    margin: 0 auto 1.5rem;
    opacity: 0.5;
}

.cart-empty p {
    font-size: 1rem;
    margin-bottom: 1.5rem;
}

/* Cart Item */
.cart-item {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    border-bottom: 1px solid #e5e7eb;
    transition: background 0.2s;
}

.cart-item:hover {
    background: #f9fafb;
}

.cart-item-image {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 0.5rem;
    flex-shrink: 0;
}

.cart-item-details {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.cart-item-name {
    font-weight: 600;
    color: #111827;
    font-size: 0.95rem;
    line-height: 1.3;
}

.cart-item-price {
    color: #3b82f6;
    font-weight: 600;
    font-size: 1rem;
}

.cart-item-quantity {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.qty-btn {
    width: 28px;
    height: 28px;
    border: 1px solid #d1d5db;
    background: #fff;
    border-radius: 0.25rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    font-size: 1.1rem;
    font-weight: 600;
    color: #6b7280;
}

.qty-btn:hover {
    background: #f3f4f6;
    border-color: #9ca3af;
}

.qty-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.qty-display {
    min-width: 30px;
    text-align: center;
    font-weight: 600;
    color: #111827;
}

.cart-item-remove {
    background: none;
    border: none;
    color: #ef4444;
    cursor: pointer;
    padding: 0.25rem;
    font-size: 0.875rem;
    transition: color 0.2s;
    text-align: left;
}

.cart-item-remove:hover {
    color: #dc2626;
    text-decoration: underline;
}

/* Footer */
.cart-sidebar-footer {
    border-top: 1px solid #e5e7eb;
    padding: 1.5rem;
    background: #f9fafb;
}

.cart-subtotal {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    font-size: 1.125rem;
    font-weight: 700;
    color: #111827;
}

.btn-view-cart,
.btn-checkout {
    display: block;
    width: 100%;
    padding: 0.875rem;
    text-align: center;
    border-radius: 0.5rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s;
    margin-bottom: 0.75rem;
}

.btn-view-cart {
    background: #fff;
    border: 2px solid #3b82f6;
    color: #3b82f6;
}

.btn-view-cart:hover {
    background: #eff6ff;
}

.btn-checkout {
    background: #3b82f6;
    color: #fff;
    border: 2px solid #3b82f6;
}

.btn-checkout:hover {
    background: #2563eb;
    border-color: #2563eb;
}

/* Responsive */
@media (max-width: 640px) {
    .cart-sidebar {
        max-width: 90%;
    }
    
    .cart-item {
        gap: 0.75rem;
        padding: 0.875rem;
    }
    
    .cart-item-image {
        width: 70px;
        height: 70px;
    }
    
    .cart-item-name {
        font-size: 0.875rem;
    }
    
    .cart-item-price {
        font-size: 0.9rem;
    }
}

@media (min-width: 641px) and (max-width: 768px) {
    .cart-sidebar {
        max-width: 380px;
    }
}
</style>
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


    <!-- Cart Sidebar Overlay -->
<div id="cartSidebar" class="cart-sidebar-overlay">
    <div class="cart-sidebar">
        <!-- Header -->
        <div class="cart-sidebar-header">
            <h3>Shopping Cart</h3>
            <button class="cart-close-btn" onclick="closeCartSidebar()">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <!-- Cart Items Container -->
        <div class="cart-sidebar-content" id="cartItemsContainer">
            <!-- Items will be loaded here dynamically -->
            <div class="cart-loading">
                <div class="spinner"></div>
                <p>Loading cart...</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="cart-sidebar-footer">
            <div class="cart-subtotal">
                <span>Subtotal:</span>
                <span id="cartSubtotal">KSh 0.00</span>
            </div>
            <a href="{{ route('cart.index') }}" class="btn-view-cart">View Full Cart</a>
            <a href="{{ route('checkout.index') }}" class="btn-checkout">Checkout</a>
        </div>
    </div>
</div>



<!-- JavaScript -->
<script>
let cartUpdateTimeout;

// Open cart sidebar
function openCartSidebar() {
    document.getElementById('cartSidebar').classList.add('active');
    document.body.style.overflow = 'hidden';
    loadCartItems();
}

// Close cart sidebar
function closeCartSidebar() {
    document.getElementById('cartSidebar').classList.remove('active');
    document.body.style.overflow = '';
}

// Close on overlay click
document.getElementById('cartSidebar')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeCartSidebar();
    }
});

// Load cart items via AJAX
function loadCartItems() {
    const container = document.getElementById('cartItemsContainer');
    
    fetch('/cart/items', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.items && data.items.length > 0) {
            container.innerHTML = data.items.map(item => `
                <div class="cart-item" data-item-id="${item.id}">
                    <img src="${item.image}" alt="${item.name}" class="cart-item-image">
                    <div class="cart-item-details">
                        <div class="cart-item-name">${item.name}</div>
                        <div class="cart-item-price">KSh ${parseFloat(item.price).toLocaleString('en-KE', {minimumFractionDigits: 2})}</div>
                        <div class="cart-item-quantity">
                            <button class="qty-btn" onclick="updateQuantity(${item.id}, ${item.quantity - 1})" ${item.quantity <= 1 ? 'disabled' : ''}>-</button>
                            <span class="qty-display">${item.quantity}</span>
                            <button class="qty-btn" onclick="updateQuantity(${item.id}, ${item.quantity + 1})" ${item.quantity >= item.stock ? 'disabled' : ''}>+</button>
                        </div>
                        <button class="cart-item-remove" onclick="removeFromCart(${item.id})">Remove</button>
                    </div>
                </div>
            `).join('');
            
            document.getElementById('cartSubtotal').textContent = 
                `KSh ${parseFloat(data.subtotal).toLocaleString('en-KE', {minimumFractionDigits: 2})}`;
        } else {
            container.innerHTML = `
                <div class="cart-empty">
                    <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                    <p>Your cart is empty</p>
                </div>
            `;
            document.getElementById('cartSubtotal').textContent = 'KSh 0.00';
        }
    })
    .catch(error => {
        console.error('Error loading cart:', error);
        container.innerHTML = '<div class="cart-empty"><p>Error loading cart</p></div>';
    });
}

// Update quantity
function updateQuantity(itemId, newQuantity) {
    if (newQuantity < 1) return;
    
    clearTimeout(cartUpdateTimeout);
    cartUpdateTimeout = setTimeout(() => {
        fetch(`/cart/update/${itemId}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ quantity: newQuantity })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadCartItems();
            }
        })
        .catch(error => console.error('Error updating cart:', error));
    }, 300);
}

// Remove from cart
function removeFromCart(itemId) {
    if (!confirm('Remove this item from cart?')) return;
    
    fetch(`/cart/remove/${itemId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadCartItems();
        }
    })
    .catch(error => console.error('Error removing item:', error));
}

// Add event listeners to "Add to Cart" buttons
document.addEventListener('DOMContentLoaded', function() {
    // Update all "Add to Cart" buttons to open sidebar
    document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            // Your existing add to cart logic here
            // Then open the sidebar
            openCartSidebar();
        });
    });
});
</script>

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