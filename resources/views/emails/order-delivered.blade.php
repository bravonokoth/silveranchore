<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Delivered</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f4f4f4; }
        .email-container { background: white; border-radius: 10px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; padding-bottom: 20px; border-bottom: 2px solid #00C851; }
        .header h1 { color: #00C851; margin: 0; font-size: 28px; }
        .delivery-icon { width: 80px; height: 80px; margin: 20px auto; background: #00C851; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .delivery-icon svg { fill: white; width: 50px; height: 50px; }
        .greeting { font-size: 18px; margin: 20px 0; }
        .highlight-box { background: linear-gradient(135deg, #00C851, #007E33); color: white; padding: 25px; border-radius: 8px; margin: 20px 0; text-align: center; }
        .highlight-box h2 { margin: 0 0 10px 0; font-size: 24px; }
        .highlight-box p { margin: 0; font-size: 16px; }
        .order-summary { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e0e0e0; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { font-weight: 600; color: #666; }
        .detail-value { color: #333; }
        .cta-button { display: inline-block; background: #00C851; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; font-weight: 600; }
        .cta-button:hover { background: #007E33; }
        .feedback-section { background: #FFF9E6; border: 2px solid #FFD700; border-radius: 8px; padding: 20px; margin: 20px 0; text-align: center; }
        .feedback-section h3 { color: #D4AF37; margin-top: 0; }
        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0e0e0; color: #666; font-size: 14px; }
        .footer a { color: #00C851; text-decoration: none; }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
        </div>

        <div class="delivery-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M18,18.5A1.5,1.5 0 0,1 16.5,17A1.5,1.5 0 0,1 18,15.5A1.5,1.5 0 0,1 19.5,17A1.5,1.5 0 0,1 18,18.5M19.5,9.5L21.46,12H17V9.5M6,18.5A1.5,1.5 0 0,1 4.5,17A1.5,1.5 0 0,1 6,15.5A1.5,1.5 0 0,1 7.5,17A1.5,1.5 0 0,1 6,18.5M20,8H17V4H3C1.89,4 1,4.89 1,6V17H3A3,3 0 0,0 6,20A3,3 0 0,0 9,17H15A3,3 0 0,0 18,20A3,3 0 0,0 21,17H23V12L20,8Z"/>
            </svg>
        </div>

        <p class="greeting">Hi {{ $customerName }},</p>

        <div class="highlight-box">
            <h2>Your Order Has Been Delivered!</h2>
            <p>Order #{{ $order->id }}</p>
        </div>

        <p>Great news! Your order has been successfully delivered to your shipping address.</p>

        <div class="order-summary">
            <h3 style="margin-top: 0;">Order Summary</h3>
            <div class="detail-row">
                <span class="detail-label">Order Number:</span>
                <span class="detail-value">#{{ $order->id }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Order Date:</span>
                <span class="detail-value">{{ $order->created_at->format('F j, Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Delivered To:</span>
                <span class="detail-value">{{ $order->shippingAddress->name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Total Amount:</span>
                <span class="detail-value" style="color: #00C851; font-weight: 600;">KSh {{ $orderTotal }}</span>
            </div>
        </div>

        <h3>Delivered To</h3>
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

        <div class="feedback-section">
            <h3>We'd Love Your Feedback!</h3>
            <p>How was your experience? Your feedback helps us improve our service.</p>
            <div style="margin-top: 15px;">
                <a href="{{ route('contact') }}" style="background: #D4AF37; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Leave a Review</a>
            </div>
        </div>

        <p style="margin-top: 30px;">Thank you for choosing {{ config('app.name') }}! We hope you enjoy your purchase.</p>

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