@extends('layouts.app')

@section('content')
<style>
    /* Reset and Base Styles */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Arial', sans-serif;
    }

    body {
        background-color: #f8f8f8;
        color: #333;
    }

    /* Wishlist Container */
    .wishlist-container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
    }

    .wishlist-container h1 {
        font-size: 2.5rem;
        color: #1a1a1a;
        text-align: center;
        margin-bottom: 30px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        border-bottom: 2px solid #2a9d8f;
        padding-bottom: 10px;
    }

    /* Empty Wishlist State */
    .wishlist-empty {
        text-align: center;
        font-size: 1.2rem;
        color: #666;
        padding: 50px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    /* Wishlist Items Grid */
    .wishlist-items {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
    }

    .wishlist-item {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .wishlist-item:hover {
        transform: translateY(-8px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .wishlist-item img {
        width: 100%;
        height: 220px;
        object-fit: cover;
        border-bottom: 1px solid #eee;
    }

    .wishlist-item-content {
        padding: 20px;
    }

    .wishlist-item-content h3 {
        font-size: 1.3rem;
        color: #1a1a1a;
        margin-bottom: 10px;
        font-weight: 600;
    }

    .wishlist-item-content p {
        font-size: 0.95rem;
        color: #666;
        margin-bottom: 15px;
        line-height: 1.5;
    }

    .wishlist-item-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
    }

    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        font-size: 0.9rem;
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.2s ease;
        text-transform: uppercase;
        font-weight: 600;
    }

    .btn-remove {
        background-color: #e63946;
        color: #fff;
    }

    .btn-remove:hover {
        background-color: #c62834;
        transform: scale(1.05);
    }

    .btn-view {
        background-color: #2a9d8f;
        color: #fff;
    }

    .btn-view:hover {
        background-color: #287c6f;
        transform: scale(1.05);
    }

    .btn-shop {
        display: block;
        width: fit-content;
        margin: 20px auto;
        background-color: #2a9d8f;
        color: #fff;
        padding: 12px 30px;
    }

    .btn-shop:hover {
        background-color: #287c6f;
        transform: scale(1.05);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .wishlist-container h1 {
            font-size: 2rem;
        }

        .wishlist-items {
            grid-template-columns: 1fr;
        }

        .wishlist-item img {
            height: 180px;
        }
    }

    @media (max-width: 480px) {
        .wishlist-container {
            padding: 0 15px;
        }

        .wishlist-item-content {
            padding: 15px;
        }

        .btn {
            padding: 8px 15px;
            font-size: 0.85rem;
        }
    }
</style>

<div class="wishlist-container">
    <h1>My Wishlist</h1>
    @if($wishlistItems->isEmpty())
        <div class="wishlist-empty">
            <p>Your wishlist is empty.</p>
            <a href="{{ route('products.index') }}" class="btn btn-shop">Continue Shopping</a>
        </div>
    @else
        <div class="wishlist-items">
            @foreach($wishlistItems as $item)
                <div class="wishlist-item">
                    @if($item->product && $item->product->image)
                        <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}">
                    @else
                        <img src="{{ asset('images/placeholder.jpg') }}" alt="Placeholder">
                    @endif
                    <div class="wishlist-item-content">
                        <h3>{{ $item->product->name ?? 'Product' }}</h3>
                        <p>{{ $item->product->description ? Str::limit($item->product->description, 50) : 'No description available' }}</p>
                        <div class="wishlist-item-actions">
                            <form action="{{ route('wishlist.destroy', $item->product_id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-remove">Remove</button>
                            </form>
                            <a href="{{ route('products.show', $item->product_id) }}" class="btn btn-view">View Product</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection