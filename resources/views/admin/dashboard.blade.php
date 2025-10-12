@extends('layouts.admin')

@section('page-title', 'Admin Dashboard')

@section('content')
<div class="analytics-grid">
    {{-- Total Products --}}
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-value">{{ \App\Models\Product::count() }}</div>
                <div class="stat-label">Products</div>
            </div>
            <div class="stat-icon blue">📦</div>
        </div>
        <div class="stat-change positive">↑ 12% from last month</div>
    </div>

    {{-- Total Orders --}}
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-value">{{ \App\Models\Order::count() }}</div>
                <div class="stat-label">Orders</div>
            </div>
            <div class="stat-icon green">🛒</div>
        </div>
        <div class="stat-change positive">↑ 8% from last month</div>
    </div>

    {{-- Total Revenue --}}
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-value">${{ number_format(\App\Models\Order::sum('total'), 2) }}</div>
                <div class="stat-label">Revenue</div>
            </div>
            <div class="stat-icon purple">💰</div>
        </div>
        <div class="stat-change positive">↑ 23% from last month</div>
    </div>

    {{-- Total Users --}}
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-value">{{ \App\Models\User::count() }}</div>
                <div class="stat-label">Users</div>
            </div>
            <div class="stat-icon orange">👥</div>
        </div>
        <div class="stat-change positive">↑ 5% from last month</div>
    </div>
</div>

{{-- Quick Actions Section --}}
<div class="quick-actions">
    <h2 class="section-title">Quick Actions</h2>
    <div class="actions-grid">
        <a href="{{ route('admin.products.index') }}" class="action-card">
            <div class="action-icon">📦</div>
            <div class="action-title">Manage Products</div>
        </a>

        <a href="{{ route('admin.categories.index') }}" class="action-card">
            <div class="action-icon">🏷️</div>
            <div class="action-title">Manage Categories</div>
        </a>

        <a href="{{ route('admin.orders.index') }}" class="action-card">
            <div class="action-icon">📋</div>
            <div class="action-title">Manage Orders</div>
        </a>

        <a href="{{ route('admin.inventories.index') }}" class="action-card">
            <div class="action-icon">📊</div>
            <div class="action-title">Manage Inventory</div>
        </a>

        <a href="{{ route('admin.coupons.index') }}" class="action-card">
            <div class="action-icon">🎟️</div>
            <div class="action-title">Manage Coupons</div>
        </a>

        <a href="{{ route('admin.banners.index') }}" class="action-card">
            <div class="action-icon">🖼️</div>
            <div class="action-title">Manage Banners</div>
        </a>

        <a href="{{ route('admin.media.index') }}" class="action-card">
            <div class="action-icon">🎨</div>
            <div class="action-title">Manage Media</div>
        </a>

        <a href="{{ route('admin.purchases.index') }}" class="action-card">
            <div class="action-icon">🛍️</div>
            <div class="action-title">Manage Purchases</div>
        </a>

        @if (auth()->user()->hasRole('super_admin'))
            <a href="{{ route('admin.users.index') }}" class="action-card">
                <div class="action-icon">👤</div>
                <div class="action-title">Manage Users</div>
            </a>
        @endif
    </div>
</div>

{{-- Recent Orders Section --}}
<div style="margin-top: 3rem;">
    <h2 class="section-title">Recent Orders</h2>
    <div style="background: white; border-radius: 0.75rem; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse(\App\Models\Order::latest()->take(5)->get() as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->user->name ?? 'Guest' }}</td>
                        <td><strong>${{ number_format($order->total, 2) }}</strong></td>
                        <td>
                            <span class="badge">{{ ucfirst($order->status) }}</span>
                        </td>
                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center; padding: 2rem; color: #64748b;">
                            No recent orders
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
