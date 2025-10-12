<!DOCTYPE html>
<html lang="en" class="light">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ $title ?? 'Admin Dashboard' }}</title>

  <!-- Feather Icons -->
  <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
  <script src="https://unpkg.com/feather-icons"></script>

  <style>
    /* === GLOBAL RESET === */
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }

    body {
      display: flex;
      height: 100vh;
      overflow: hidden;
      transition: background 0.3s, color 0.3s;
    }

    a { text-decoration: none; color: inherit; }

    /* === LIGHT THEME === */
    body.light {
      background: #f9fafb;
      color: #1e293b;
    }

    body.light .sidebar { background: #111827; color: #d1d5db; }
    body.light .navbar { background: #fff; border-bottom: 1px solid #e5e7eb; }
    body.light .stat-card, body.light .action-card { background: #fff; color: #1e293b; }

    /* === DARK THEME === */
    body.dark {
      background: #0f172a;
      color: #e2e8f0;
    }

    body.dark .sidebar { background: #0f172a; color: #94a3b8; }
    body.dark .navbar { background: #1e293b; border-bottom: 1px solid #334155; }
    body.dark .stat-card, body.dark .action-card { background: #1e293b; color: #e2e8f0; }
    body.dark .stat-icon.blue { background: #1d4ed8; color: #eff6ff; }
    body.dark .stat-icon.green { background: #059669; color: #ecfdf5; }
    body.dark .stat-icon.purple { background: #7c3aed; color: #f5f3ff; }
    body.dark .stat-icon.orange { background: #ea580c; color: #fff7ed; }

    /* === SIDEBAR === */
    .sidebar {
      width: 250px;
      display: flex;
      flex-direction: column;
      padding-top: 1rem;
      transition: all 0.3s;
    }

    .sidebar .logo {
      text-align: center;
      color: #fff;
      font-size: 1.4rem;
      font-weight: 700;
      margin-bottom: 1.5rem;
    }

    .nav-links { flex: 1; display: flex; flex-direction: column; }

    .nav-links a {
      padding: 12px 24px;
      display: flex;
      align-items: center;
      font-size: 0.95rem;
      transition: 0.3s;
      color: inherit;
    }

    .nav-links a:hover,
    .nav-links a.active {
      background: rgba(255,255,255,0.1);
      color: #fff;
    }

    /* === MAIN AREA === */
    .main { flex: 1; display: flex; flex-direction: column; height: 100%; }

    /* === NAVBAR === */
    .navbar {
      height: 64px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 24px;
      transition: all 0.3s;
    }

    .navbar h1 {
      font-size: 1.2rem;
      font-weight: 600;
    }

    .navbar .right {
      display: flex;
      align-items: center;
      gap: 16px;
    }

    .navbar .right button {
      background: none;
      border: none;
      cursor: pointer;
      padding: 6px;
      border-radius: 50%;
      transition: 0.3s;
      color: inherit;
    }

    .navbar .right button:hover {
      background: rgba(255,255,255,0.1);
    }

    .profile {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .profile img { width: 38px; height: 38px; border-radius: 50%; }

    .profile .info p { font-size: 0.9rem; font-weight: 600; }
    .profile .info span { font-size: 0.75rem; opacity: 0.7; }

    /* === CONTENT === */
    .content {
      flex: 1;
      padding: 24px;
      overflow-y: auto;
      transition: background 0.3s;
    }

    /* === DARK MODE TOGGLE === */
    .theme-toggle {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 34px;
      height: 34px;
      border-radius: 50%;
      background: none;
      border: 1px solid #cbd5e1;
      cursor: pointer;
      transition: 0.3s;
    }

    body.dark .theme-toggle {
      border: 1px solid #475569;
    }
  </style>
</head>

<body class="light">
  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="logo">SilverAnchor</div>

    <nav class="nav-links">
      <a href="{{ route('admin.dashboard') }}" class="active"><i data-feather="home"></i>&nbsp; Dashboard</a>
      <a href="{{ route('admin.products.index') }}"><i data-feather="box"></i>&nbsp; Products</a>
      <a href="{{ route('admin.categories.index') }}"><i data-feather="tag"></i>&nbsp; Categories</a>
      <a href="{{ route('admin.orders.index') }}"><i data-feather="shopping-cart"></i>&nbsp; Orders</a>
      <a href="{{ route('admin.inventories.index') }}"><i data-feather="bar-chart-2"></i>&nbsp; Inventory</a>
      <a href="{{ route('admin.coupons.index') }}"><i data-feather="gift"></i>&nbsp; Coupons</a>
      <a href="{{ route('admin.banners.index') }}"><i data-feather="image"></i>&nbsp; Banners</a>
      <a href="{{ route('admin.media.index') }}"><i data-feather="camera"></i>&nbsp; Media</a>
      <a href="{{ route('admin.purchases.index') }}"><i data-feather="shopping-bag"></i>&nbsp; Purchases</a>
    </nav>
  </aside>

  <!-- MAIN -->
  <div class="main">
    <header class="navbar">
      <h1>@yield('page-title', 'Dashboard')</h1>

      <div class="right">
        <button class="theme-toggle" id="theme-toggle" title="Toggle Dark Mode">
          <i data-feather="moon"></i>
        </button>

        <button><i data-feather="bell"></i></button>
        <button><i data-feather="settings"></i></button>

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

    // === DARK MODE TOGGLE ===
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
  </script>
</body>
</html>
