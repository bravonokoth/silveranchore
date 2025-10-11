@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">My Orders</h2>
        @if (session('success'))
            <div class="bg-green-100 text-green-800 p-4 rounded mb-4">{{ session('success') }}</div>
        @endif
        @if ($orders->isEmpty())
            <p class="text-gray-600">No orders found.</p>
            <a href="{{ route('products.index') }}" class="text-blue-600 hover:underline">Continue Shopping</a>
        @else
            <div class="bg-white rounded shadow overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="p-3 text-left">Order ID</th>
                            <th class="p-3 text-left">Date</th>
                            <th class="p-3 text-left">Total</th>
                            <th class="p-3 text-left">Status</th>
                            <th class="p-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td class="p-3">#{{ $order->id }}</td>
                                <td class="p-3">{{ $order->created_at->format('M d, Y') }}</td>
                                <td class="p-3">KSh {{ number_format($order->total, 2) }}</td>
                                <td class="p-3">{{ ucfirst($order->status) }}</td>
                                <td class="p-3">
                                    <a href="{{ route('orders.show', $order) }}" class="text-blue-600 hover:underline">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection