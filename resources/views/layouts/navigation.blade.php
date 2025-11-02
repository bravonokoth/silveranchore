<nav x-data="{ open: false }" class="navbar-wine">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center" style="height: 70px;">
            <!-- Left: Logo + Site Name -->
            <div class="flex items-center" style="min-width: 150px;">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('images/silver.png') }}" alt="Logo" class="h-12 w-auto">
                </a>
                <span class="ml-3 text-2xl font-bold text-white hidden md:block">
                    Silveranchor
                </span>
            </div>

            <!-- Center: Search Bar (Desktop) -->
            <div class="hidden sm:flex flex-1 max-w-2xl mx-8">
                <form action="{{ route('products.index') }}" method="GET" class="search-form w-full">
                    @foreach(request()->except(['search', 'page']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach

                    <input 
                        type="text" 
                        name="search" 
                        class="search-input" 
                        placeholder="Search for liquors, wines, spirits..." 
                        value="{{ request('search') }}"
                        autocomplete="off"
                    >
                    <button type="submit" class="search-button">
                        <i class="fas fa-search"></i>
                        <span class="hidden md:inline">Search</span>
                    </button>
                </form>
            </div>

            <!-- NEW: Navigation Dropdown (Between Search & Icons) -->
            <div class="hidden sm:flex items-center mx-4">
                <div x-data="{ open: false }" class="relative">
                    <button 
                        @click="open = !open" 
                        @click.outside="open = false"
                        class="flex items-center gap-2 px-4 py-2 text-white font-medium rounded-lg hover:bg-white hover:bg-opacity-10 transition duration-200"
                        aria-haspopup="true"
                        :aria-expanded="open"
                    >
                        <i class="fas fa-bars"></i>
                        <span class="hidden md:inline">Menu</span>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div 
                        x-show="open" 
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute top-full left-1/2 transform -translate-x-1/2 mt-2 w-48 bg-white rounded-lg shadow-xl z-50 overflow-hidden"
                        style="display: none;"
                        x-cloak
                    >
                        <a href="{{ route('home') }}" 
                           class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2 {{ request()->routeIs('home') ? 'bg-gray-50 font-semibold' : '' }}">
                            <i class="fas fa-home w-4"></i> Home
                        </a>
                        @auth
                            <a href="{{ Auth::user()->hasRole(['super-admin', 'admin']) ? route('admin.dashboard') : route('dashboard') }}" 
                               class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2 {{ request()->routeIs('admin.dashboard', 'dashboard') ? 'bg-gray-50 font-semibold' : '' }}">
                                <i class="fas fa-tachometer-alt w-4"></i> Dashboard
                            </a>
                        @endauth
                        <a href="{{ route('products.index') }}" 
                           class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2 {{ request()->routeIs('products.index') ? 'bg-gray-50 font-semibold' : '' }}">
                            <i class="fas fa-wine-bottle w-4"></i> Products
                        </a>
                        <a href="{{ route('categories.index') }}" 
                           class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2 {{ request()->routeIs('categories.index') ? 'bg-gray-50 font-semibold' : '' }}">
                            <i class="fas fa-th-large w-4"></i> Categories
                        </a>
                        <a href="{{ route('about') }}" 
                           class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2 {{ request()->routeIs('about') ? 'bg-gray-50 font-semibold' : '' }}">
                            <i class="fas fa-info-circle w-4"></i> About
                        </a>
                        <a href="{{ route('contact') }}" 
                           class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2 {{ request()->routeIs('contact') ? 'bg-gray-50 font-semibold' : '' }}">
                            <i class="fas fa-envelope w-4"></i> Contact
                        </a>
                        @auth
                            <a href="{{ route('orders.index') }}" 
                               class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2 {{ request()->routeIs('orders.index') ? 'bg-gray-50 font-semibold' : '' }}">
                                <i class="fas fa-box w-4"></i> Orders
                            </a>
                        @endauth
                    </div>
                </div>
            </div>

            <!-- Right: Icons and User Dropdown -->
            <div class="hidden sm:flex items-center gap-4">
                <!-- Wishlist Icon -->
                <a href="{{ route('wishlist.index') }}" class="nav-icon-link" title="Wishlist">
                    <i class="fas fa-heart"></i>
                    @php
                        $wishlistCount = 0;
                        if (auth()->check()) {
                            $wishlistCount = auth()->user()->wishlist()->count();
                        } else {
                            $wishlistCount = \App\Models\Wishlist::where('user_id', session('wishlist_user_id', 'guest_' . uniqid()))->count();
                        }
                    @endphp
                    @if($wishlistCount > 0)
                        <span class="nav-badge">{{ $wishlistCount }}</span>
                    @endif
                </a>
                
                <!-- Cart Icon -->
                <a href="{{ route('cart.index') }}" class="nav-icon-link" title="Shopping Cart">
                    <i class="fas fa-shopping-cart"></i>
                    @php
                        $cartCount = 0;
                        if (session()->has('cart')) {
                            foreach (session('cart') as $item) {
                                $cartCount += $item['quantity'] ?? 1;
                            }
                        }
                    @endphp
                    @if($cartCount > 0)
                        <span class="nav-badge">{{ $cartCount }}</span>
                    @endif
                </a>

                @auth
                    <!-- User Dropdown (Logged In) -->
                    <div x-data="{ dropdownOpen: false }" class="relative">
                        <button @click="dropdownOpen = !dropdownOpen" class="user-dropdown-trigger">
                            <i class="fas fa-user"></i>
                            <span>{{ Auth::user()->name }}</span>
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="dropdownOpen" 
                             @click.outside="dropdownOpen = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95"
                             class="user-dropdown-menu"
                             style="display: none;"
                             x-cloak>
                            <a href="{{ route('profile.edit') }}"><i class="fas fa-user"></i> <span>Profile</span></a>
                            <a href="{{ route('orders.index') }}"><i class="fas fa-box"></i> <span>My Orders</span></a>
                            <a href="{{ route('addresses.index') }}"><i class="fas fa-map-marker-alt"></i> <span>Addresses</span></a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"><i class="fas fa-sign-out-alt"></i> <span>Log Out</span></button>
                            </form>
                        </div>
                    </div>
                @else
                    <!-- Auth Dropdown (Not Logged In) -->
                    <div x-data="{ authDropdownOpen: false }" class="relative">
                        <button @click="authDropdownOpen = !authDropdownOpen" class="user-dropdown-trigger">
                            <i class="fas fa-user"></i>
                            <span>Account</span>
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="authDropdownOpen" 
                             @click.outside="authDropdownOpen = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95"
                             class="user-dropdown-menu"
                             style="display: none;"
                             x-cloak>
                            <a href="{{ route('login') }}"><i class="fas fa-sign-in-alt"></i> <span>Login</span></a>
                            <a href="{{ route('register') }}"><i class="fas fa-user-plus"></i> <span>Register</span></a>
                        </div>
                    </div>
                @endauth
            </div>

            <!-- Hamburger (Mobile) -->
            <div class="flex items-center sm:hidden">
                <button @click="open = ! open" class="hamburger">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- REMOVED: Secondary Navigation Bar (now in dropdown) -->

    <!-- Responsive Navigation Menu (Mobile) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden mobile-menu">
        <!-- Mobile Search -->
        <div class="mobile-search">
            <form action="{{ route('products.index') }}" method="GET" class="search-form">
                @foreach(request()->except(['search', 'page']) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <input type="text" name="search" class="search-input" placeholder="Search products..." value="{{ request('search') }}" autocomplete="off">
                <button type="submit" class="search-button"><i class="fas fa-search"></i></button>
            </form>
        </div>

        <!-- Mobile Icons -->
        <div class="mobile-icons">
            <a href="{{ route('wishlist.index') }}" class="nav-icon-link" title="Wishlist">
                <i class="fas fa-heart"></i>
                @if($wishlistCount > 0)<span class="nav-badge">{{ $wishlistCount }}</span>@endif
            </a>
            <a href="{{ route('cart.index') }}" class="nav-icon-link" title="Shopping Cart">
                <i class="fas fa-shopping-cart"></i>
                @if($cartCount > 0)<span class="nav-badge">{{ $cartCount }}</span>@endif
            </a>
        </div>

        <!-- Mobile Navigation Links -->
        <div class="mobile-nav-links">
            <a href="{{ route('home') }}" class="mobile-nav-link {{ request()->routeIs('home') ? 'active' : '' }}"><i class="fas fa-home"></i> Home</a>
            @auth
                <a href="{{ Auth::user()->hasRole(['super-admin', 'admin']) ? route('admin.dashboard') : route('dashboard') }}" class="mobile-nav-link {{ request()->routeIs('admin.dashboard', 'dashboard') ? 'active' : '' }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            @endauth
            <a href="{{ route('products.index') }}" class="mobile-nav-link {{ request()->routeIs('products.index') ? 'active' : '' }}"><i class="fas fa-wine-bottle"></i> Products</a>
            <a href="{{ route('categories.index') }}" class="mobile-nav-link {{ request()->routeIs('categories.index') ? 'active' : '' }}"><i class="fas fa-th-large"></i> Categories</a>
            <a href="{{ route('about') }}" class="mobile-nav-link {{ request()->routeIs('about') ? 'active' : '' }}"><i class="fas fa-info-circle"></i> About</a>
            <a href="{{ route('contact') }}" class="mobile-nav-link {{ request()->routeIs('contact') ? 'active' : '' }}"><i class="fas fa-envelope"></i> Contact</a>
            @auth
                <a href="{{ route('addresses.index') }}" class="mobile-nav-link {{ request()->routeIs('addresses.index') ? 'active' : '' }}"><i class="fas fa-map-marker-alt"></i> Addresses</a>
                <a href="{{ route('orders.index') }}" class="mobile-nav-link {{ request()->routeIs('orders.index') ? 'active' : '' }}"><i class="fas fa-box"></i> Orders</a>
            @endauth
        </div>

        <!-- Mobile Auth -->
        @auth
            <div class="pt-4 pb-1 border-t border-white border-opacity-20">
                <div class="px-4 mb-3">
                    <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-300">{{ Auth::user()->email }}</div>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('profile.edit') }}" class="mobile-nav-link"><i class="fas fa-user"></i> Profile</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="mobile-nav-link w-full text-left"><i class="fas fa-sign-out-alt"></i> Log Out</button>
                    </form>
                </div>
            </div>
        @else
            <div class="mobile-auth-buttons">
                <a href="{{ route('login') }}" class="mobile-nav-link"><i class="fas fa-sign-in-alt"></i> Login</a>
                <a href="{{ route('register') }}" class="mobile-nav-link"><i class="fas fa-user-plus"></i> Register</a>
            </div>
        @endauth
    </div>
</nav>

<style>
[x-cloak] {
    display: none !important;
}

/* Wine Background for Navbar */
.navbar-wine {
    background: #722f37;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    position: relative;
    z-index: 50;
}

/* Search Form Styling */
.search-form {
    display: flex;
    align-items: center;
    background: white;
    border-radius: 50px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.search-input {
    flex: 1;
    padding: 12px 24px;
    border: none;
    outline: none;
    font-size: 15px;
    color: #333;
    background: transparent;
}

.search-input::placeholder {
    color: #999;
}

.search-button {
    padding: 12px 28px;
    background: #d69e2e;
    border: none;
    color: white;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.3s ease;
    display: flex;
    align-items: center;
    gap: 6px;
}

.search-button:hover {
    background: #b7791f;
}

/* Navigation Icons */
.nav-icon-link {
    position: relative;
    color: white;
    font-size: 1.4rem;
    transition: all 0.3s ease;
    padding: 8px;
}

.nav-icon-link:hover {
    color: #ecc94b;
    transform: scale(1.1);
}

.nav-badge {
    position: absolute;
    top: 0;
    right: 0;
    background: #dc3545;
    color: white;
    font-size: 10px;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 10px;
    min-width: 18px;
    text-align: center;
}

/* User Dropdown */
.user-dropdown-trigger {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: rgba(255, 255, 255, 0.1);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 50px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.user-dropdown-trigger:hover {
    background: rgba(255, 255, 255, 0.2);
}

.user-dropdown-menu {
    position: absolute;
    top: 100%;
    right: 0;
    z-index: 50;
    min-width: 200px;
    padding: 8px 0;
    margin-top: 8px;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.user-dropdown-menu a,
.user-dropdown-menu button {
    display: flex;
    align-items: center;
    gap: 10px;
    width: 100%;
    padding: 10px 16px;
    color: #333;
    font-size: 14px;
    text-align: left;
    background: none;
    border: 0;
    text-decoration: none;
    transition: all 0.2s ease;
}

.user-dropdown-menu a:hover,
.user-dropdown-menu button:hover {
    background-color: #f7fafc;
}

.dropdown-divider {
    height: 0;
    margin: 8px 0;
    overflow: hidden;
    border-top: 1px solid #e9ecef;
}

/* Hamburger */
.hamburger {
    color: white;
    padding: 8px;
}

/* Mobile Menu */
.mobile-menu {
    background: #722f37;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.mobile-search {
    padding: 1rem;
}

.mobile-icons {
    display: flex;
    justify-content: center;
    gap: 2rem;
    padding: 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.mobile-nav-links {
    display: flex;
    flex-direction: column;
    padding: 0.5rem 0;
}

.mobile-nav-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 1rem;
    color: white;
    text-decoration: none;
    font-size: 15px;
    transition: background 0.3s ease;
}

.mobile-nav-link:hover,
.mobile-nav-link.active {
    background: rgba(255, 255, 255, 0.1);
    color: #ecc94b;
}

.mobile-auth-buttons {
    display: flex;
    flex-direction: column;
    padding: 1rem;
    gap: 0.5rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}
</style>