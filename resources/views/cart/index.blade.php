@extends('layouts.app')

@section('content')
    <div class="cart-wrapper">
        <div class="cart-container">
            <div class="cart-header">
                <h2>Your Cart</h2>
            </div>

            @if (session('success'))
                <div class="cart-message success">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="cart-message error">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="15" y1="9" x2="9" y2="15"></line>
                        <line x1="9" y1="9" x2="15" y2="15"></line>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            @if ($cartItems->isEmpty())
                <div class="empty-cart">
                    <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#c0a062" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 30px;">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                    <p>Your cart is empty.</p>
                    <a href="{{ route('products.index') }}" class="continue-shopping">Continue Shopping</a>
                </div>
            @else
                <div class="cart-table-wrapper">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cartItems as $item)
                                @if ($item->product)
                                    <tr>
                                        <td>
                                            <div class="product-info">
                                                <img 
                                                    src="{{ $item->product->media->where('type', 'image')->first() ? asset('storage/' . $item->product->media->where('type', 'image')->first()->path) : 'https://via.placeholder.com/150' }}" 
                                                    alt="{{ $item->product->name }}" 
                                                    class="product-image"
                                                >
                                                <span class="product-name">{{ $item->product->name }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="product-price">KSh {{ number_format($item->product->price, 2) }}</span>
                                        </td>
                                        <td>
                                            <form action="{{ route('cart.update', $item) }}" method="POST" class="quantity-form">
                                                @csrf
                                                @method('PUT')
                                                <input 
                                                    type="number" 
                                                    name="quantity" 
                                                    value="{{ $item->quantity }}" 
                                                    min="1" 
                                                    max="{{ $item->product->stock }}" 
                                                    class="quantity-input"
                                                >
                                                <button type="submit" class="update-btn">Update</button>
                                            </form>
                                        </td>
                                        <td>
                                            <span class="product-total">KSh {{ number_format($item->product->price * $item->quantity, 2) }}</span>
                                        </td>
                                        <td>
                                            <form action="{{ route('cart.destroy', $item) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button 
                                                    type="submit" 
                                                    class="remove-btn" 
                                                    onclick="return confirm('Remove this item from cart?')"
                                                >
                                                    Remove
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="cart-summary">
                    <p class="cart-total">Total: <span>KSh {{ number_format($total, 2) }}</span></p>
                    <a href="{{ route('checkout.index') }}" class="checkout-btn">Proceed to Checkout</a>
                </div>
            @endif
        </div>
    </div>
@endsection