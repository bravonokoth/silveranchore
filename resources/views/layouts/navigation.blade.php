<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                        {{ __('Home') }}
                    </x-nav-link>
                    @auth
                        <x-nav-link :href="Auth::user()->hasRole(['super-admin', 'admin']) ? route('admin.dashboard') : route('dashboard')" :active="request()->routeIs('admin.dashboard', 'dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                    @endauth
                    <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.index')">
                        {{ __('Products') }}
                    </x-nav-link>
                    <x-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.index')">
                        {{ __('Categories') }}
                    </x-nav-link>
                    <x-nav-link :href="route('about')" :active="request()->routeIs('about')">
                        {{ __('About') }}
                    </x-nav-link>
                    <x-nav-link :href="route('contact')" :active="request()->routeIs('contact')">
                        {{ __('Contact') }}
                    </x-nav-link>
                    @auth
                        <x-nav-link :href="route('addresses.index')" :active="request()->routeIs('addresses.index')">
                            {{ __('Addresses') }}
                        </x-nav-link>
                        <x-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.index')">
                            {{ __('Orders') }}
                        </x-nav-link>
                    @endauth
                </div>
            </div>

            <!-- Right Side: Icons and Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Navigation Icons -->
                <div class="nav-icons">
                    <!-- Wishlist Icon -->
                    <a href="{{ route('wishlist.index') }}" class="nav-icon-link" title="Wishlist">
                        <i class="fas fa-heart"></i>
                        @auth
                            @php
                                $wishlistCount = Auth::user()->wishlistItems()->count();
                            @endphp
                            @if($wishlistCount > 0)
                                <span class="nav-badge">{{ $wishlistCount }}</span>
                            @endif
                        @endauth
                    </a>
                    
                    <!-- Cart Icon -->
                    <a href="{{ route('cart.index') }}" class="nav-icon-link" title="Shopping Cart">
                        <i class="fas fa-shopping-cart"></i>
                        @php
                            $cartCount = 0;
                            if(session()->has('cart')) {
                                foreach(session('cart') as $item) {
                                    $cartCount += $item['quantity'] ?? 1;
                                }
                            }
                        @endphp
                        @if($cartCount > 0)
                            <span class="nav-badge">{{ $cartCount }}</span>
                        @endif
                    </a>
                </div>

                @auth
                    <!-- User Dropdown (Logged In) -->
                    <div x-data="{ dropdownOpen: false }" class="user-dropdown">
                        <button @click="dropdownOpen = !dropdownOpen" class="user-dropdown-trigger">
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
                            <a href="{{ route('profile.edit') }}">
                                <i class="fas fa-user"></i>
                                <span>Profile</span>
                            </a>
                            <a href="{{ route('orders.index') }}">
                                <i class="fas fa-box"></i>
                                <span>My Orders</span>
                            </a>
                            <a href="{{ route('addresses.index') }}">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Addresses</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span>Log Out</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <!-- Auth Dropdown (Not Logged In) -->
                    <div x-data="{ authDropdownOpen: false }" class="user-dropdown">
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
                            <a href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>Login</span>
                            </a>
                            <a href="{{ route('register') }}">
                                <i class="fas fa-user-plus"></i>
                                <span>Register</span>
                            </a>
                        </div>
                    </div>
                @endauth
            </div>

            <!-- Hamburger (Mobile) -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="hamburger">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (Mobile) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden mobile-menu">
        <!-- Mobile Icons -->
        <div class="mobile-icons">
            <a href="{{ route('wishlist.index') }}" class="nav-icon-link" title="Wishlist">
                <i class="fas fa-heart"></i>
                @auth
                    @php
                        $wishlistCount = Auth::user()->wishlistItems()->count();
                    @endphp
                    @if($wishlistCount > 0)
                        <span class="nav-badge">{{ $wishlistCount }}</span>
                    @endif
                @endauth
            </a>
            <a href="{{ route('cart.index') }}" class="nav-icon-link" title="Shopping Cart">
                <i class="fas fa-shopping-cart"></i>
                @php
                    $cartCount = 0;
                    if(session()->has('cart')) {
                        foreach(session('cart') as $item) {
                            $cartCount += $item['quantity'] ?? 1;
                        }
                    }
                @endphp
                @if($cartCount > 0)
                    <span class="nav-badge">{{ $cartCount }}</span>
                @endif
            </a>
        </div>

        <!-- Mobile Navigation Links -->
        <div class="mobile-nav-links">
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                {{ __('Home') }}
            </x-responsive-nav-link>
            @auth
                <x-responsive-nav-link :href="Auth::user()->hasRole(['super-admin', 'admin']) ? route('admin.dashboard') : route('dashboard')" :active="request()->routeIs('admin.dashboard', 'dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
            @endauth
            <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.index')">
                {{ __('Products') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.index')">
                {{ __('Categories') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('about')" :active="request()->routeIs('about')">
                {{ __('About') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('contact')" :active="request()->routeIs('contact')">
                {{ __('Contact') }}
            </x-responsive-nav-link>
            @auth
                <x-responsive-nav-link :href="route('addresses.index')" :active="request()->routeIs('addresses.index')">
                    {{ __('Addresses') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.index')">
                    {{ __('Orders') }}
                </x-responsive-nav-link>
            @endauth
        </div>

        <!-- Mobile Auth Section -->
        @auth
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @else
            <!-- Mobile Auth Buttons -->
            <div class="mobile-auth-buttons">
                <a href="{{ route('login') }}" class="mobile-nav-link">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Login</span>
                </a>
                <a href="{{ route('register') }}" class="mobile-nav-link">
                    <i class="fas fa-user-plus"></i>
                    <span>Register</span>
                </a>
            </div>
        @endauth
    </div>
</nav>

<style>
[x-cloak] {
    display: none !important;
}
</style>