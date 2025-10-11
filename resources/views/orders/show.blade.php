@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Order #{{ $order->id }}</h2>
        <div class="bg-white rounded shadow p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-xl font-semibold mb-2">Order Details</h3>
                    <p><strong>Total:</strong> KSh {{ number_format($order->total, 2) }}</p>
                    <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
                    <p><strong>Date:</strong> {{ $order->created_at->format('M d, Y') }}</p>
                </div>
                <div>
                    <h3 class="text-xl font-semibold mb-2">Delivery Address</h3>
                    <p>{{ $order->shippingAddress->first_name }} {{ $order->shippingAddress->last_name }}</p>
                    <p>{{ $order->shippingAddress->email }}</p>
                    <p>{{ $order->shippingAddress->phone_number }}</p>
                    <p>{{ $order->shippingAddress->line1 }}</p>
                    @if ($order->shippingAddress->line2)
                        <p>{{ $order->shippingAddress->line2 }}</p>
                    @endif
                    <p>{{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} {{ $order->shippingAddress->postal_code }}</p>
                    <p>{{ $order->shippingAddress->country }}</p>
                    @if ($order->billing_address_id !== $order->shipping_address_id)
                        <h3 class="text-xl font-semibold mt-4 mb-2">Billing Address</h3>
                        <p>{{ $order->billingAddress->first_name }} {{ $order->billingAddress->last_name }}</p>
                        <p>{{ $order->billingAddress->email }}</p>
                        <p>{{ $order->billingAddress->phone_number }}</p>
                        <p>{{ $order->billingAddress->line1 }}</p>
                        @if ($order->billingAddress->line2)
                            <p>{{ $order->billingAddress->line2 }}</p>
                        @endif
                        <p>{{ $order->billingAddress->city }}, {{ $order->billingAddress->state }} {{ $order->billingAddress->postal_code }}</p>
                        <p>{{ $order->billingAddress->country }}</p>
                    @endif
                </div>
            </div>
            <h3 class="text-xl font-semibold mt-6 mb-2">Order Items</h3>
            <div class="overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="p-3 text-left">Product</th>
                            <th class="p-3 text-left">Price</th>
                            <th class="p-3 text-left">Quantity</th>
                            <th class="p-3 text-left">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td class="p-3">{{ $item->product->name }}</td>
                                <td class="p-3">KSh {{ number_format($item->price, 2) }}</td>
                                <td class="p-3">{{ $item->quantity }}</td>
                                <td class="p-3">KSh {{ number_format($item->price * $item->quantity, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <a href="{{ route('orders.index') }}" class="mt-4 inline-block text-blue-600 hover:underline">Back to Orders</a>
    </div>
@endsection