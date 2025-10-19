@extends('layouts.app')
@section('content')
    <div class="success-page">
        <h1>Order Confirmed! #{{ $order->id }}</h1>
        <p>Amount: KSh {{ number_format($order->total, 2) }}</p>
        <p>Check your email for details.</p>
        <a href="{{ route('products.index') }}">Continue Shopping</a>
    </div>
@endsection