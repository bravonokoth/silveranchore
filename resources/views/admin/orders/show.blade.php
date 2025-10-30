@extends('layouts.admin')

@section('page-title', 'Order #' . $order->id)

@section('content')
<div class="max-w-6xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold">Order #{{ $order->id }}</h2>
            <div>
                <a href="{{ route('admin.orders.edit', $order) }}" class="bg-yellow-600 text-white py-2 px-4 rounded hover:bg-yellow-700">Edit Order</a>
                <form action="{{ route('admin.orders.drop', $order) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to cancel this order?')">
                    @csrf
                    @method('POST')
                    <button type="submit" class="bg-red-600 text-white py-2 px-4 rounded hover:bg-red-700 ml-2">Cancel Order</button>
                </form>
                <a href="{{ route('admin.orders.index') }}" class="bg-gray-600 text-white py-2 px-4 rounded hover:bg-gray-700 ml-2">Back to Orders</a>
            </div>
        </div>

        {{-- Customer Details --}}
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-2">Customer Information</h3>
            <p><strong>Name:</strong> {{ $order->user ? $order->user->name : ($order->shippingAddress ? $order->shippingAddress->name : 'Guest') }}</p>
            <p><strong>Email:</strong> {{ $order->email ?? ($order->shippingAddress ? $order->shippingAddress->email : 'N/A') }}</p>
            <p><strong>Phone:</strong> {{ $order->shippingAddress ? $order->shippingAddress->phone_number : 'N/A' }}</p>
        </div>

        {{-- Shipping Address --}}
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-2">Shipping Address</h3>
            @if($order->shippingAddress)
                <p>{{ $order->shippingAddress->name }}</p>
                <p>{{ $order->shippingAddress->line1 }}{{ $order->shippingAddress->line2 ? ', ' . $order->shippingAddress->line2 : '' }}</p>
                <p>{{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state ?? '' }} {{ $order->shippingAddress->postal_code ?? '' }}</p>
                <p>{{ $order->shippingAddress->country }}</p>
                <p><strong>Phone:</strong> {{ $order->shippingAddress->phone_number }}</p>
            @else
                <p class="text-gray-500">No shipping address provided.</p>
            @endif
        </div>

        {{-- Billing Address (if different) --}}
        @if($order->billingAddress && $order->billing_address_id !== $order->shipping_address_id)
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-2">Billing Address</h3>
            <p>{{ $order->billingAddress->name }}</p>
            <p>{{ $order->billingAddress->line1 }}{{ $order->billingAddress->line2 ? ', ' . $order->billingAddress->line2 : '' }}</p>
            <p>{{ $order->billingAddress->city }}, {{ $order->billingAddress->state ?? '' }} {{ $order->billingAddress->postal_code ?? '' }}</p>
            <p>{{ $order->billingAddress->country }}</p>
            <p><strong>Phone:</strong> {{ $order->billingAddress->phone_number }}</p>
        </div>
        @endif

        {{-- Order Items --}}
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-4">Order Items</h3>
            <table class="w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border p-3 text-left">Product</th>
                        <th class="border p-3 text-left">Quantity</th>
                        <th class="border p-3 text-left">Unit Price</th>
                        <th class="border p-3 text-left">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->items as $item)
                    <tr>
                        <td class="border p-3">
                            <div class="flex items-center">
                                @if($item->product->media->first())
                                    <img src="{{ asset('storage/' . $item->product->media->first()->path) }}" alt="{{ $item->product->name }}" class="w-12 h-12 object-cover mr-3 rounded">
                                @endif
                                <span>{{ $item->product->name }}</span>
                            </div>
                        </td>
                        <td class="border p-3">{{ $item->quantity }}</td>
                        <td class="border p-3">KSh {{ number_format($item->price, 2) }}</td>
                        <td class="border p-3">KSh {{ number_format($item->price * $item->quantity, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="border p-3 text-center text-gray-500">No items found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Total and Status --}}
        <div class="p-4 bg-gray-50 rounded-lg">
            <p><strong>Total Amount:</strong> KSh {{ number_format($order->total, 2) }}</p>
            <p><strong>Order Status:</strong>
                <span class="px-2 py-1 rounded text-sm {{ $order->status == 'pending' ? 'bg-yellow-200 text-yellow-800' : 'bg-green-200 text-green-800' }}">
                    {{ ucfirst($order->status) }}
                </span>
            </p>
            <p><strong>Payment Status:</strong>
                <span class="px-2 py-1 rounded text-sm {{ $order->payment_status == 'pending' ? 'bg-yellow-200 text-yellow-800' : 'bg-green-200 text-green-800' }}">
                    {{ ucfirst($order->payment_status) }}
                </span>
            </p>
            <p><strong>Payment Method:</strong> {{ ucfirst($order->payment_method ?? 'N/A') }}</p>
            @if($order->paid_at)
                <p><strong>Paid At:</strong> {{ $order->paid_at->format('Y-m-d H:i:s') }}</p>
            @endif
        </div>
    </div>
</div>
@endsection