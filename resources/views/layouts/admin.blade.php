<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin Dashboard' }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">

    <!-- Scripts -->
    <script src="https://unpkg.com/feather-icons"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="admin-layout light">
    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <div class="logo">
            <img src="{{ asset('images/silver.png') }}" alt="SilverAnchor Logo" class="logo-img">
            <span class="logo-text">SilverAnchor</span>
        </div>
        <nav class="nav-links">
            <a href="{{ route('admin.dashboard') }}" class="nav-link active"><i data-feather="home"></i><span>Dashboard</span></a>
            <a href="{{ route('admin.categories.index') }}" class="nav-link"><i data-feather="tag"></i><span>Categories</span></a>
            <a href="{{ route('admin.products.index') }}" class="nav-link"><i data-feather="box"></i><span>Products</span></a>
            <a href="{{ route('admin.orders.index') }}" class="nav-link"><i data-feather="shopping-cart"></i><span>Orders</span></a>
            <a href="{{ route('admin.inventories.index') }}" class="nav-link"><i data-feather="bar-chart-2"></i><span>Inventory</span></a>
            <a href="{{ route('admin.coupons.index') }}" class="nav-link"><i data-feather="gift"></i><span>Coupons</span></a>
            <a href="{{ route('admin.banner.index') }}" class="nav-link"><i data-feather="image"></i><span>Banners</span></a>
            <a href="{{ route('admin.media.index') }}" class="nav-link"><i data-feather="camera"></i><span>Media</span></a>
            <a href="{{ route('admin.purchases.index') }}" class="nav-link"><i data-feather="shopping-bag"></i><span>Purchases</span></a>
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"><i data-feather="users"></i><span>Users</span>
</a>
        </nav>
    </aside>

    <!-- MAIN -->
    <div class="main" id="main">
        <header class="navbar">
            <div class="navbar-left">
                <button class="sidebar-toggle" id="sidebar-toggle" title="Toggle Sidebar">
                    <i data-feather="menu"></i>
                </button>
                <h1>@yield('page-title', 'Dashboard')</h1>
            </div>
            <div class="navbar-right">
                <button class="theme-toggle" id="theme-toggle" title="Toggle Theme">
                    <i data-feather="moon"></i>
                </button>
                <button class="icon-btn" title="Settings"><i data-feather="settings"></i></button>
                <div x-data="{ open: false }" class="profile-dropdown">
                    <button @click="open = !open" class="profile">
                        <div class="info">
                            <p>{{ auth()->user()->name ?? 'Admin User' }}</p>
                            <span>{{ auth()->user()->role ?? 'Administrator' }}</span>
                        </div>
                        <i data-feather="chevron-down" class="chevron"></i>
                    </button>
                    <div x-show="open" @click.outside="open = false" class="dropdown-menu">
                        <a href="{{ route('profile.edit') }}" class="dropdown-item">
                            <i data-feather="user" class="dropdown-icon"></i>
                            {{ __('Profile') }}
                        </a>
                        <a href="{{ route('notifications.index') }}" class="dropdown-item">
                            <i data-feather="bell" class="dropdown-icon"></i>
                            {{ __('Notifications') }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}" class="dropdown-item"
                               onclick="event.preventDefault(); this.closest('form').submit();">
                                <i data-feather="log-out" class="dropdown-icon"></i>
                                {{ __('Log Out') }}
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="content">
            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    <script>
        feather.replace();

        // === THEME TOGGLE ===
        const themeToggle = document.getElementById('theme-toggle');
        const body = document.body;
        const storedTheme = localStorage.getItem('theme');

        if (storedTheme === 'dark') {
            body.classList.add('dark');
            themeToggle.innerHTML = feather.icons.sun.toSvg();
        }

        themeToggle.addEventListener('click', () => {
            const isDark = body.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            themeToggle.innerHTML = isDark ? feather.icons.sun.toSvg() : feather.icons.moon.toSvg();
        });

        // === SIDEBAR TOGGLE ===
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('sidebar');
        const main = document.getElementById('main');

        if (localStorage.getItem('sidebar') === 'collapsed') {
            sidebar.classList.add('collapsed');
            main.classList.add('expanded');
        }

        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            main.classList.toggle('expanded');
            localStorage.setItem('sidebar', sidebar.classList.contains('collapsed') ? 'collapsed' : 'expanded');
        });
    </script>

    <style>
        /* Inline helper styles */
        .logo {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: bold;
            padding: 1rem;
        }
        .logo-img {
            width: 36px;
            height: 36px;
            object-fit: contain;
        }
        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #333;
            color: #fff;
            padding: 12px 16px;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.25);
            z-index: 9999;
            animation: fadeInOut 6s ease-in-out forwards;
        }
        @keyframes fadeInOut {
            0% { opacity: 0; transform: translateY(20px); }
            10% { opacity: 1; transform: translateY(0); }
            90% { opacity: 1; transform: translateY(0); }
            100% { opacity: 0; transform: translateY(20px); }
        }
    </style>

    <!-- Page-specific scripts -->
    @stack('scripts')

</body>
</html>