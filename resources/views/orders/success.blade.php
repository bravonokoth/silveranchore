@extends('layouts.app')

@section('content')
<div class="success-container">
    <div class="success-card">
        <div class="success-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#00c851" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
        </div>
        
        <h1>Payment Successful!</h1>
        <p>Thank you for your order</p>
        
        <div class="order-details">
            <div class="detail-row">
                <span>Order Number:</span>
                <strong>#{{ $order->id }}</strong>
            </div>
            <div class="detail-row">
                <span>Total Amount:</span>
                <strong>KSh {{ number_format($order->total, 2) }}</strong>
            </div>
            <div class="detail-row">
                <span>Payment Status:</span>
                <span class="badge-success">{{ ucfirst($order->payment_status) }}</span>
            </div>
        </div>
        
        <div class="action-buttons">
            <a href="{{ route('orders.show', $order) }}" class="btn btn-primary">View Order</a>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">Continue Shopping</a>
        </div>
    </div>
</div>

<style>
    .success-container {
        max-width: 600px;
        margin: 50px auto;
        padding: 20px;
    }
    .success-card {
        background: white;
        border-radius: 12px;
        padding: 40px;
        text-align: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .success-icon {
        margin-bottom: 20px;
    }
    .order-details {
        margin: 30px 0;
        text-align: left;
    }
    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .badge-success {
        background: #00c851;
        color: white;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.9rem;
    }
    .action-buttons {
        display: flex;
        gap: 15px;
        margin-top: 30px;
    }
    .btn {
        flex: 1;
        padding: 12px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-primary {
        background: #00C3F7;
        color: white;
    }
    .btn-secondary {
        background: #f8f9fa;
        color: #333;
    }
</style>
@endsection