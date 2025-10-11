@extends('layouts.admin')

@section('content')
    <div class="max-w-6xl mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Admin Dashboard</h2>
        <div class="grid grid-cols-3 gap-4">
            <a href="{{ route('admin.products.index') }}" class="bg-blue-600 text-white p-4 rounded hover:bg-blue-700">Manage Products</a>
            <a href="{{ route('admin.categories.index') }}" class="bg-blue-600 text-white p-4 rounded hover:bg-blue-700">Manage Categories</a>
            <a href="{{ route('admin.orders.index') }}" class="bg-blue-600 text-white p-4 rounded hover:bg-blue-700">Manage Orders</a>
            <a href="{{ route('admin.inventories.index') }}" class="bg-blue-600 text-white p-4 rounded hover:bg-blue-700">Manage Inventory</a>
            <a href="{{ route('admin.coupons.index') }}" class="bg-blue-600 text-white p-4 rounded hover:bg-blue-700">Manage Coupons</a>
            <a href="{{ route('admin.banners.index') }}" class="bg-blue-600 text-white p-4 rounded hover:bg-blue-700">Manage Banners</a>
            <a href="{{ route('admin.media.index') }}" class="bg-blue-600 text-white p-4 rounded hover:bg-blue-700">Manage Media</a>
            <a href="{{ route('admin.purchases.index') }}" class="bg-blue-600 text-white p-4 rounded hover:bg-blue-700">Manage Purchases</a>
            @if (auth()->user()->hasRole('super_admin'))
                <a href="{{ route('admin.users.index') }}" class="bg-blue-600 text-white p-4 rounded hover:bg-blue-700">Manage Users</a>
            @endif
        </div>
    </div>
@endsection