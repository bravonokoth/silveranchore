<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ $title ?? 'Admin Dashboard' }}</title>

  <!-- Feather Icons -->
  <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
  <script src="https://unpkg.com/feather-icons"></script>
</head>

<body class="admin-layout light">
  <!-- SIDEBAR -->
  <aside class="sidebar" id="sidebar">
    <div class="logo">SilverAnchor</div>

    <nav class="nav-links">
      <a href="{{ route('admin.dashboard') }}" class="nav-link active"><i data-feather="home"></i><span>Dashboard</span></a>
      <a href="{{ route('admin.products.index') }}" class="nav-link"><i data-feather="box"></i><span>Products</span></a>
      <a href="{{ route('admin.categories.index') }}" class="nav-link"><i data-feather="tag"></i><span>Categories</span></a>
      <a href="{{ route('admin.orders.index') }}" class="nav-link"><i data-feather="shopping-cart"></i><span>Orders</span></a>
      <a href="{{ route('admin.inventories.index') }}" class="nav-link"><i data-feather="bar-chart-2"></i><span>Inventory</span></a>
      <a href="{{ route('admin.coupons.index') }}" class="nav-link"><i data-feather="gift"></i><span>Coupons</span></a>
      <a href="{{ route('admin.banners.index') }}" class="nav-link"><i data-feather="image"></i><span>Banners</span></a>
      <a href="{{ route('admin.media.index') }}" class="nav-link"><i data-feather="camera"></i><span>Media</span></a>
      <a href="{{ route('admin.purchases.index') }}" class="nav-link"><i data-feather="shopping-bag"></i><span>Purchases</span></a>
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
        <button class="icon-btn"><i data-feather="bell"></i></button>
        <button class="icon-btn"><i data-feather="settings"></i></button>
        <div class="profile">
          <img src="https://i.pravatar.cc/40" alt="Profile" />
          <div class="info">
            <p>{{ auth()->user()->name ?? 'Admin User' }}</p>
            <span>{{ auth()->user()->role ?? 'Administrator' }}</span>
          </div>
        </div>
      </div>
    </header>

    <main class="content">
      @yield('content')
    </main>
  </div>

  <script>
    feather.replace();

    // Theme Toggle
    const themeToggle = document.getElementById('theme-toggle');
    const body = document.body;
    const storedTheme = localStorage.getItem('theme');

    if (storedTheme === 'dark') {
      body.classList.add('dark');
      body.classList.remove('light');
      themeToggle.innerHTML = feather.icons.sun.toSvg();
    }

    themeToggle.addEventListener('click', () => {
      body.classList.toggle('dark');
      body.classList.toggle('light');
      const isDark = body.classList.contains('dark');
      localStorage.setItem('theme', isDark ? 'dark' : 'light');
      themeToggle.innerHTML = isDark ? feather.icons.sun.toSvg() : feather.icons.moon.toSvg();
    });

    // Sidebar Toggle
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('sidebar');
    const main = document.getElementById('main');

    sidebarToggle.addEventListener('click', () => {
      sidebar.classList.toggle('collapsed');
      main.classList.toggle('expanded');
      localStorage.setItem('sidebar', sidebar.classList.contains('collapsed') ? 'collapsed' : 'expanded');
    });

    // Load sidebar state
    if (localStorage.getItem('sidebar') === 'collapsed') {
      sidebar.classList.add('collapsed');
      main.classList.add('expanded');
    }
  </script>
</body>
</html>