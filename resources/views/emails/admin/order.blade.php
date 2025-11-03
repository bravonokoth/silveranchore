<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Order Alert</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f4f4f4; }
        .email-container { background: white; border-radius: 10px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; padding-bottom: 20px; border-bottom: 2px solid #FF6B6B; }
        .header h1 { color: #FF6B6B; margin: 0; font-size: 28px; }
        .alert-icon { width: 80px; height: 80px; margin: 20px auto; background: #FF6B6B; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .alert-icon svg { fill: white; width: 50px; height: 50px; }
        .greeting { font-size: 18px; margin: 20px 0; }
        .order-details { background: #fff5f5; padding: 20px; border-radius: 8px; margin: 20px 0; border: 1px solid #ffe0e0; }
        .order-details h2 { color: #FF6B6B; margin-top: 0; font-size: 20px; }
        .detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #ffe0e0; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { font-weight: 600; color: #666; }
        .detail-value { color: #333; }
        .products-table { width: 100%; margin: 20px 0; border-collapse: collapse; }
        .products-table th { background: #fff5f5; padding: 12px; text-align: left; font-weight: 600; color: #666; border-bottom: 2px solid #ffe0e0; }
        .products-table td { padding: 12px; border-bottom: 1px solid #f0f0f0; }
        .total-row { font-weight: bold; font-size: 18px; color: #FF6B6B; }
        .cta-button { display: inline-block; background: #FF6B6B; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; font-weight: 600; }
        .cta-button:hover { background: #e55a5a; }
        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0e0e0; color: #666; font-size: 14px; }
        .footer a { color: #FF6B6B; text-decoration: none; }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>{{ config('app.name') }} Admin</h1>
        </div>

        <div class="alert-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
            </svg>
        </div>

        <p class="greeting">New Order Received!</p>

        <p>A new order has been placed and payment is confirmed. Please review and process it.</p>

        <div class="order-details">
            <h2>Order Summary</h2>
            <div class="detail-row">
                <span class="detail-label">Order Number:</span>
                <span class="detail-value">#{{ $order->id }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Customer:</span>
                <span class="detail-value">{{ $customerName }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Email:</span>
                <span class="detail-value">{{ $order->shippingAddress->email }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Phone:</span>
                <span class="detail-value">{{ $order->shippingAddress->phone_number }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Total:</span>
                <span class="detail-value" style="color: #FF6B6B; font-weight: 600;">KSh {{ $orderTotal }}</span>
            </div>
        </div>

        <h3 style="margin-top: 30px;">Items Ordered</h3>
        <table class="products-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>KSh {{ number_format($item->price, 2) }}</td>
                        <td>KSh {{ number_format($item->price * $item->quantity, 2) }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="3" style="text-align: right;">Total:</td>
                    <td>KSh {{ $orderTotal }}</td>
                </tr>
            </tbody>
        </table>

        <h3>Shipping Address</h3>
        <p style="margin: 10px 0; line-height: 1.8; background: #f8f9fa; padding: 15px; border-radius: 8px;">
            <strong>{{ $order->shippingAddress->name }}</strong><br>
            {{ $order->shippingAddress->line1 }}<br>
            @if($order->shippingAddress->line2)
                {{ $order->shippingAddress->line2 }}<br>
            @endif
            {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} {{ $order->shippingAddress->postal_code }}<br>
            {{ $order->shippingAddress->country }}<br>
            Phone: {{ $order->shippingAddress->phone_number }}
        </p>

        <div style="text-align: center;">
            <a href="{{ route('admin.orders.show', $order) }}" class="cta-button">Review Order in Admin Panel</a>
        </div>

        <div class="footer">
            <p>This is an automated alert from {{ config('app.name') }}.</p>
            <p>
                <a href="{{ route('admin.dashboard') }}">Admin Dashboard</a> | 
                <a href="{{ route('admin.orders.index') }}">All Orders</a>
            </p>
            <p style="margin-top: 15px; font-size: 12px; color: #999;">
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>