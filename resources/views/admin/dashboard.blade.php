@extends('layouts.admin')

@section('page-title', 'Admin Dashboard')

@section('content')
     Analytics Cards 
    <div class="analytics-grid">
         Total Products 
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-value">{{ \App\Models\Product::count() }}</div>
                    <div class="stat-label">Total Products</div>
                </div>
                <div class="stat-icon blue">
                    📦
                </div>
            </div>
            <div class="stat-change positive">
                ↑ 12% from last month
            </div>
        </div>

         Total Orders 
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-value">{{ \App\Models\Order::count() }}</div>
                    <div class="stat-label">Total Orders</div>
                </div>
                <div class="stat-icon green">
                    🛒
                </div>
            </div>
            <div class="stat-change positive">
                ↑ 8% from last month
            </div>
        </div>

         Revenue 
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-value">${{ number_format(\App\Models\Order::sum('total'), 2) }}</div>
                    <div class="stat-label">Total Revenue</div>
                </div>
                <div class="stat-icon purple">
                    💰
                </div>
            </div>
            <div class="stat-change positive">
                ↑ 23% from last month
            </div>
        </div>

         Active Users 
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-value">{{ \App\Models\User::count() }}</div>
                    <div class="stat-label">Total Users</div>
                </div>
                <div class="stat-icon orange">
                    👥
                </div>
            </div>
            <div class="stat-change positive">
                ↑ 5% from last month
            </div>
        </div>
    </div>

     Quick Actions Section 
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

     Recent Activity Section (Optional) 
    <div style="margin-top: 3rem;">
        <h2 class="section-title">Recent Orders</h2>
        <div style="background: white; border-radius: 0.75rem; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid #e2e8f0;">
                        <th style="padding: 0.75rem; text-align: left; font-weight: 600; color: #64748b;">Order ID</th>
                        <th style="padding: 0.75rem; text-align: left; font-weight: 600; color: #64748b;">Customer</th>
                        <th style="padding: 0.75rem; text-align: left; font-weight: 600; color: #64748b;">Amount</th>
                        <th style="padding: 0.75rem; text-align: left; font-weight: 600; color: #64748b;">Status</th>
                        <th style="padding: 0.75rem; text-align: left; font-weight: 600; color: #64748b;">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(\App\Models\Order::latest()->take(5)->get() as $order)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 0.75rem; color: #1e293b;">#{{ $order->id }}</td>
                            <td style="padding: 0.75rem; color: #1e293b;">{{ $order->user->name ?? 'Guest' }}</td>
                            <td style="padding: 0.75rem; color: #1e293b; font-weight: 600;">${{ number_format($order->total, 2) }}</td>
                            <td style="padding: 0.75rem;">
                                <span style="padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background-color: #dbeafe; color: #1e40af;">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem; color: #64748b;">{{ $order->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding: 2rem; text-align: center; color: #64748b;">No recent orders</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
