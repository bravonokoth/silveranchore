@extends('layouts.app')

@section('content')
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Message (Moved to Top) -->
            <div class="bg-white overflow-hidden shadow mb-8 rounded wide-section">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in, ") . Auth::user()->name . "! Welcome to your dashboard." }}
                </div>
            </div>

            <!-- Order Analytics Card -->
            <div class="bg-white overflow-hidden shadow mb-8 rounded wide-section">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Order Analytics</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                        <div class="bg-blue-200 rounded p-4 text-center">
                            <p class="text-sm text-gray-600">Total Orders</p>
                            <p class="text-xl font-bold text-gray-800">{{ $totalOrders }}</p>
                        </div>
                        <div class="bg-green-200 rounded p-4 text-center">
                            <p class="text-sm text-gray-600">Total Spent</p>
                            <p class="text-xl font-bold text-gray-800">KSh {{ number_format($totalSpent, 2) }}</p>
                        </div>
                        <div class="bg-yellow-200 rounded p-4 text-center">
                            <p class="text-sm text-gray-600">Pending Orders</p>
                            <p class="text-xl font-bold text-gray-800">{{ $ordersByStatus['pending'] ?? 0 }}</p>
                        </div>
                    </div>
                    <!-- Pie Chart: Orders by Status  -->
                    <div class="mt-6">
                        <canvas id="ordersByStatusChart" height="50"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="bg-white overflow-hidden shadow mb-8 rounded wide-section">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Recent Orders</h3>
                        <a href="{{ route('orders.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                            View All Orders
                        </a>
                    </div>
                    @if($orders->isEmpty())
                        <p class="text-gray-600">You have no orders yet.</p>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($orders as $order)
                                <div class="bg-light-gray rounded shadow hover:shadow-md transition-shadow p-4">
                                    <div class="flex justify-between items-center mb-3">
                                        <h4 class="text-md font-medium text-gray-800">Order #{{ $order->id }}</h4>
                                        <span class="text-sm text-gray-500">{{ $order->created_at->format('M d, Y') }}</span>
                                    </div>
                                    <div class="space-y-2 mb-3">
                                        @foreach($order->items->take(2) as $item)
                                            <div class="flex items-center space-x-3">
                                                @if($item->product && $item->product->image)
                                                    <div class="relative w-16 h-16">
                                                        <img
                                                            src="{{ asset('storage/' . $item->product->image) }}"
                                                            alt="{{ $item->product->name }}"
                                                            class="w-full h-auto object-contain rounded"
                                                            loading="lazy"
                                                        />
                                                    </div>
                                                @endif
                                                <div>
                                                    <p class="text-sm font-medium text-gray-700">{{ $item->product->name ?? 'Unknown Product' }}</p>
                                                    <p class="text-xs text-gray-500">Qty: {{ $item->quantity }} | KSh {{ number_format($item->price, 2) }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                        @if($order->items->count() > 2)
                                            <p class="text-xs text-gray-500">+ {{ $order->items->count() - 2 }} more items</p>
                                        @endif
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-semibold text-gray-800">Total: KSh {{ number_format($order->total, 2) }}</span>
                                        <span class="text-xs px-2 py-1 rounded {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Address Analytics Card -->
            <div class="bg-white overflow-hidden shadow mb-8 rounded wide-section">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Address Analytics</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                        <div class="bg-purple-200 rounded p-4 text-center">
                            <p class="text-sm text-gray-600">Total Addresses</p>
                            <p class="text-xl font-bold text-gray-800">{{ $totalAddresses }}</p>
                        </div>
                        <div class="bg-red-200 rounded p-4 text-center">
                            <p class="text-sm text-gray-600">Shipping Addresses</p>
                            <p class="text-xl font-bold text-gray-800">{{ $shippingAddresses }}</p>
                        </div>
                    </div>
                    <!-- Removed Address Chart -->
                </div>
            </div>

            <!-- Recent Addresses -->
            <div class="bg-white overflow-hidden shadow mb-8 rounded wide-section">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Your Addresses</h3>
                        <a href="{{ route('addresses.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                            Manage Addresses
                        </a>
                    </div>
                    @if($addresses->isEmpty())
                        <p class="text-gray-600">You have no addresses saved.</p>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($addresses as $address)
                                <div class="bg-light-gray rounded shadow hover:shadow-md transition-shadow p-4">
                                    <div class="flex items-start space-x-3">
                                        <i class="fas fa-map-marker-alt text-gray-600 mt-1"></i>
                                        <div>
                                            <p class="text-sm font-medium text-gray-700">{{ $address->line1 }}</p>
                                            @if($address->line2)
                                                <p class="text-sm text-gray-600">{{ $address->line2 }}</p>
                                            @endif
                                            <p class="text-sm text-gray-600">{{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}</p>
                                            <p class="text-sm text-gray-600">{{ $address->country }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            try {
                // Pie Chart: Orders by Status
                const ordersByStatusCtx = document.getElementById('ordersByStatusChart').getContext('2d');
                new Chart(ordersByStatusCtx, {
                    type: 'pie',
                    data: {
                        labels: @json(array_keys($ordersByStatus)),
                        datasets: [{
                            label: 'Orders by Status',
                            data: @json(array_values($ordersByStatus)),
                            backgroundColor: [
                                'rgba(59, 130, 246, 0.5)',
                                'rgba(239, 68, 68, 0.5)',
                                'rgba(16, 185, 129, 0.5)',
                                'rgba(245, 158, 11, 0.5)'
                            ],
                            borderColor: [
                                'rgba(59, 130, 246, 1)',
                                'rgba(239, 68, 68, 1)',
                                'rgba(16, 185, 129, 1)',
                                'rgba(245, 158, 11, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        plugins: {
                            legend: { position: 'top' },
                            title: { display: true, text: 'Orders by Status' }
                        }
                    }
                });
            } catch (error) {
                console.error('Chart.js Error:', error);
            }
        });
    </script>

    <!-- Embedded CSS -->
    <style>
        .py-12 { padding-top: 3rem; padding-bottom: 3rem; }
        .max-w-7xl { max-width: 80rem; }
        .mx-auto { margin-left: auto; margin-right: auto; }
        .sm\:px-6 { padding-left: 1.5rem; padding-right: 1.5rem; }
        .lg\:px-8 { padding-left: 2rem; padding-right: 2rem; }
        .bg-white { background-color: #ffffff; }
        .overflow-hidden { overflow: hidden; }
        .shadow { box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1), 0 1px 2px rgba(0, 0, 0, 0.06); }
        .mb-8 { margin-bottom: 2rem; }
        .rounded { border-radius: 0.25rem; }
        .p-6 { padding: 1.5rem; }
        .text-gray-900 { color: #1a202c; }
        .text-lg { font-size: 1.125rem; }
        .font-semibold { font-weight: 600; }
        .text-gray-800 { color: #2d3748; }
        .mb-4 { margin-bottom: 1rem; }
        .grid { display: grid; }
        .grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
        @media (min-width: 640px) {
            .sm\:grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .sm\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (min-width: 1024px) {
            .lg\:grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }
        .gap-4 { gap: 1rem; }
        .gap-6 { gap: 1.5rem; }
        .bg-light-gray { background-color: #f7fafc; }
        .p-4 { padding: 1rem; }
        .text-center { text-align: center; }
        .text-sm { font-size: 0.875rem; }
        .text-xl { font-size: 1.25rem; }
        .font-bold { font-weight: 700; }
        .text-gray-600 { color: #4a5568; }
        .mt-6 { margin-top: 1.5rem; }
        .flex { display: flex; }
        .justify-between { justify-content: space-between; }
        .items-center { align-items: center; }
        .text-md { font-size: 1rem; }
        .text-xs { font-size: 0.75rem; }
        .space-y-2 > * + * { margin-top: 0.5rem; }
        .space-x-3 > * + * { margin-left: 0.75rem; }
        .w-16 { width: 4rem; }
        .h-16 { height: 4rem; }
        .object-contain { object-fit: contain; }
        .rounded-md { border-radius: 0.375rem; }
        .hover\:shadow-md:hover { box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
        .transition-shadow { transition: box-shadow 0.3s ease; }
        .px-2 { padding-left: 0.5rem; padding-right: 0.5rem; }
        .py-1 { padding-top: 0.25rem; padding-bottom: 0.25rem; }
        .bg-green-100 { background-color: #f0fff4; }
        .text-green-800 { color: #276749; }
        .bg-yellow-100 { background-color: #fefcbf; }
        .text-yellow-800 { color: #975a16; }
        .wide-section { width: 85%; max-width: none; margin-left: auto; margin-right: auto; } /* Increased to 85% width */
        .bg-blue-200 { background-color: #bfdbfe; }
        .bg-green-200 { background-color: #bbf7d0; }
        .bg-yellow-200 { background-color: #fef9c3; }
        .bg-purple-200 { background-color: #e9d5ff; }
        .bg-red-200 { background-color: #fee2e2; }
    </style>
@endsection