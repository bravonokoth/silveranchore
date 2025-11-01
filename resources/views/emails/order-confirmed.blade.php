<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f4f4f4; }
        .email-container { background: white; border-radius: 10px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; padding-bottom: 20px; border-bottom: 2px solid #00C3F7; }
        .header h1 { color: #00C3F7; margin: 0; font-size: 28px; }
        .success-icon { width: 80px; height: 80px; margin: 20px auto; background: #00C851; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .success-icon svg { fill: white; width: 50px; height: 50px; }
        .greeting { font-size: 18px; margin: 20px 0; }
        .order-details { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .order-details h2 { color: #333; margin-top: 0; font-size: 20px; }
        .detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e0e0e0; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { font-weight: 600; color: #666; }
        .detail-value { color: #333; }
        .products-table { width: 100%; margin: 20px 0; border-collapse: collapse; }
        .products-table th { background: #f8f9fa; padding: 12px; text-align: left; font-weight: 600; color: #666; border-bottom: 2px solid #e0e0e0; }
        .products-table td { padding: 12px; border-bottom: 1px solid #f0f0f0; }
        .total-row { font-weight: bold; font-size: 18px; color: #00C851; }
        .cta-button { display: inline-block; background: #00C3F7; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; font-weight: 600; }
        .cta-button:hover { background: #0099CC; }
        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0e0e0; color: #666; font-size: 14px; }
        .footer a { color: #00C3F7; text-decoration: none; }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
        </div>

        <div class="success-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
            </svg>
        </div>

        <p class="greeting">Hi {{ $customerName }},</p>

        <p>Thank you for your order! We're excited to confirm that we've received your payment and your order is being processed.</p>

        <div class="order-details">
            <h2>Order Details</h2>
            <div class="detail-row">
                <span class="detail-label">Order Number:</span>
                <span class="detail-value">#{{ $order->id }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Order Date:</span>
                <span class="detail-value">{{ $order->created_at->format('F j, Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Payment Status:</span>
                <span class="detail-value" style="color: #00C851; font-weight: 600;">{{ ucfirst($order->payment_status) }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Payment Method:</span>
                <span class="detail-value">{{ ucfirst($order->payment_method) }}</span>
            </div>
        </div>

        <h3 style="margin-top: 30px;">Items Ordered</h3>
        <table class="products-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
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
        <p style="margin: 10px 0; line-height: 1.8;">
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
            <a href="{{ route('orders.show', $order) }}" class="cta-button">View Order Details</a>
        </div>

        <p style="margin-top: 30px;">We'll send you another email when your order has been shipped.</p>

        <div class="footer">
            <p>Thank you for shopping with us!</p>
            <p>
                <a href="{{ route('home') }}">Visit our website</a> | 
                <a href="{{ route('contact') }}">Contact Support</a>
            </p>
            <p style="margin-top: 15px; font-size: 12px; color: #999;">
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>