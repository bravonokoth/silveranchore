@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6">
    <h2 class="text-3xl font-bold mb-6 text-gray-800">🛒 My Orders</h2>

    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl mb-6 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if ($orders->isEmpty())
        <div class="text-center py-10">
            <p class="text-gray-600 text-lg mb-6">You haven’t placed any orders yet.</p>
            <a href="{{ route('products.index') }}" 
               class="inline-block px-5 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition duration-200 shadow">
                Continue Shopping
            </a>
        </div>

        {{-- Dummy visual placeholders --}}
        <h3 class="text-xl font-semibold mt-10 mb-4 text-gray-700">Preview of your future orders 👇</h3>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
                $dummyOrders = [
                    ['id' => 1012, 'date' => 'Oct 21, 2025', 'total' => 4599.50, 'status' => 'completed'],
                    ['id' => 1013, 'date' => 'Oct 20, 2025', 'total' => 1249.00, 'status' => 'pending'],
                    ['id' => 1014, 'date' => 'Oct 18, 2025', 'total' => 999.99, 'status' => 'cancelled'],
                ];
            @endphp

            @foreach ($dummyOrders as $order)
                @php
                    $statusColor = match($order['status']) {
                        'pending' => 'bg-yellow-100 text-yellow-700',
                        'completed' => 'bg-green-100 text-green-700',
                        'cancelled' => 'bg-red-100 text-red-700',
                        default => 'bg-gray-100 text-gray-700'
                    };
                @endphp

                <div class="bg-white border border-gray-100 rounded-2xl shadow hover:shadow-lg transition duration-200">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-sm text-gray-500">Order ID</span>
                            <span class="font-semibold text-gray-800">#{{ $order['id'] }}</span>
                        </div>

                        <div class="mb-3">
                            <p class="text-sm text-gray-500 mb-1">Date</p>
                            <p class="font-medium text-gray-800">{{ $order['date'] }}</p>
                        </div>

                        <div class="mb-3">
                            <p class="text-sm text-gray-500 mb-1">Total</p>
                            <p class="font-semibold text-green-600">KSh {{ number_format($order['total'], 2) }}</p>
                        </div>

                        <div class="mb-5">
                            <p class="text-sm text-gray-500 mb-1">Status</p>
                            <span class="inline-block px-3 py-1 text-sm font-medium rounded-full {{ $statusColor }}">
                                {{ ucfirst($order['status']) }}
                            </span>
                        </div>

                        <div class="text-right">
                            <a href="#" 
                               class="inline-block px-4 py-2 text-sm bg-gray-300 text-gray-600 rounded-xl cursor-not-allowed">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($orders as $order)
                <div class="bg-white border border-gray-100 rounded-2xl shadow hover:shadow-lg transition duration-200">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-sm text-gray-500">Order ID</span>
                            <span class="font-semibold text-gray-800">#{{ $order->id }}</span>
                        </div>

                        <div class="mb-3">
                            <p class="text-sm text-gray-500 mb-1">Date</p>
                            <p class="font-medium text-gray-800">{{ $order->created_at->format('M d, Y') }}</p>
                        </div>

                        <div class="mb-3">
                            <p class="text-sm text-gray-500 mb-1">Total</p>
                            <p class="font-semibold text-green-600">KSh {{ number_format($order->total, 2) }}</p>
                        </div>

                        <div class="mb-5">
                            <p class="text-sm text-gray-500 mb-1">Status</p>
                            @php
                                $statusColor = match($order->status) {
                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                    'completed' => 'bg-green-100 text-green-700',
                                    'cancelled' => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-100 text-gray-700'
                                };
                            @endphp
                            <span class="inline-block px-3 py-1 text-sm font-medium rounded-full {{ $statusColor }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>

                        <div class="text-right">
                            <a href="{{ route('orders.show', $order) }}" 
                               class="inline-block px-4 py-2 text-sm bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition duration-150">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
