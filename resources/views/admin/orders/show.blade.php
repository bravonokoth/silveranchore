@extends('layouts.admin')

@section('content')
    <div class="max-w-lg mx-auto p-6 bg-white rounded shadow">
        <h2 class="text-2xl font-bold mb-4">Order #{{ $order->id }}</h2>
        <p><strong>Customer:</strong> {{ $order->user ? $order->user->name : $order->email }}</p>
        <p><strong>Shipping Address:</strong> {{ $order->shipping_address }}</p>
        <p><strong>Total:</strong> ${{ $order->total }}</p>
        <p><strong>Status:</strong> {{ $order->status }}</p>
        <a href="{{ route('admin.orders.edit', $order) }}" class="bg-blue-600 text-white py-2 px-4 rounded mt-4 inline-block">Edit</a>
    </div>
@endsection