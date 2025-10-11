<!DOCTYPE html>
<html>
<head>
    <title>SilverAnchorE - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow">
        <div class="container mx-auto px-4 py-2 flex justify-between items-center">
            <a href="{{ route('admin.dashboard') }}" class="text-lg font-bold">SilverAnchorE Admin</a>
            <div>
                <a href="{{ route('admin.products.index') }}" class="text-blue-600 hover:text-blue-800 mx-2">Products</a>
                <a href="{{ route('admin.categories.index') }}" class="text-blue-600 hover:text-blue-800 mx-2">Categories</a>
                <a href="{{ route('admin.orders.index') }}" class="text-blue-600 hover:text-blue-800 mx-2">Orders</a>
                <a href="{{ route('admin.inventories.index') }}" class="text-blue-600 hover:text-blue-800 mx-2">Inventory</a>
                <a href="{{ route('admin.coupons.index') }}" class="text-blue-600 hover:text-blue-800 mx-2">Coupons</a>
                <a href="{{ route('admin.banners.index') }}" class="text-blue-600 hover:text-blue-800 mx-2">Banners</a>
                <a href="{{ route('admin.media.index') }}" class="text-blue-600 hover:text-blue-800 mx-2">Media</a>
                <a href="{{ route('admin.purchases.index') }}" class="text-blue-600 hover:text-blue-800 mx-2">Purchases</a>
                @if (auth()->user()->hasRole('super_admin'))
                    <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:text-blue-800 mx-2">Users</a>
                @endif
                <form action="{{ route('auth.logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-red-600 hover:text-red-800 mx-2">Logout</button>
                </form>
            </div>
        </div>
    </nav>
    <div class="container mx-auto px-4 py-6">
        @if (session('success'))
            <div class="bg-green-100 text-green-800 p-4 rounded mb-4">{{ session('success') }}</div>
        @endif
        @yield('content')
    </div>
    <script>
        window.Laravel = {
            userId: {{ auth()->id() ?? 'null' }}
        };
    </script>
</body>
</html>