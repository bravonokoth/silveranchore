{{-- resources/views/partials/product-card.blade.php --}}
<div class="card">
    <a href="{{ route('products.show', $product->id) }}" class="position-relative">
        <img class="card-img-top" 
             src="{{ asset('storage/' . ($product->media->first()?->path ?? 'images/placeholder.jpg')) }}" 
             alt="{{ $product->name }}">

        @if($product->stock == 0)
            <div class="position-absolute top-0 start-0 pt-3 ps-3">
                <span class="badge badge-danger">Sold Out</span>
            </div>
        @else
            <div class="position-absolute top-0 start-0 pt-3 ps-3">
                <span class="badge badge-success">In Stock</span>
            </div>
        @endif

        <div class="position-absolute top-0 end-0 pt-3 pe-3">
            <button type="button" class="btn btn-sm btn-icon btn-outline-secondary rounded-circle">
                <span class="fas fa-heart btn-icon__inner"></span>
            </button>
        </div>
    </a>

    <div class="card-body">
        <div class="product-left">
            <h3>
                <a href="{{ route('products.show', $product->id) }}">
                    {{ $product->name }}
                </a>
            </h3>
            <a href="{{ route('categories.show', $product->category_id) }}" class="product-category">
                {{ $product->category?->name ?? 'Uncategorized' }}
            </a>
        </div>

        <div class="product-right">
            <div class="price-info">
                @if($product->discount_price && $product->discount_price < $product->price)
                    <span class="current-price">Ksh {{ number_format($product->discount_price, 0) }}</span>
                    <span class="original-price">Ksh {{ number_format($product->price, 0) }}</span>
                @else
                    <span class="current-price">Ksh {{ number_format($product->price, 0) }}</span>
                @endif
            </div>

            <div class="stock-info {{ $product->stock == 0 ? 'out-of-stock' : '' }}">
                @if($product->stock > 0)
                    <i class="fas fa-check-circle"></i> {{ $product->stock }} in stock
                @else
                    <i class="fas fa-times-circle"></i> Out of stock
                @endif
            </div>
        </div>
    </div>

    <div class="card-footer border-0">
        <div class="action-buttons">
            <form action="{{ route('cart.store') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
                @if($product->stock > 0)
                    <button type="submit" class="add-to-cart-btn">
                        <i class="fas fa-shopping-cart"></i> Add to Cart
                    </button>
                @else
                    <button type="button" class="add-to-cart-btn btn-disabled" disabled>
                        <i class="fas fa-ban"></i> Sold Out
                    </button>
                @endif
            </form>

            @if($product->stock > 0)
                <a href="{{ route('checkout.quick', $product->id) }}" class="buy-now-btn">
                    <i class="fas fa-bolt"></i> Buy Now
                </a>
            @endif
        </div>
    </div>
</div>

<style>
/* CARD STYLE  */
.card {
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(74, 144, 226, 0.12);
    transition: all 0.3s ease;
    height: auto;
    min-height: 420px;
    display: flex;
    flex-direction: column;
}

.card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 35px rgba(74, 144, 226, 0.25);
}

.position-relative {
    position: relative;
    height: 270px;
    background: #f0f7ff;
    overflow: hidden;
}

.card-img-top {
    width: 100%;
    height: 270px;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.card:hover .card-img-top {
    transform: scale(1.08);
}

.card-body {
    padding: 0.6rem 1rem;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.4rem;
    flex-grow: 1;
}

.product-left h3 {
    font-size: 0.95rem;
    font-weight: 600;
    margin: 0;
    line-height: 1.2;
}

.product-left h3 a {
    color: #1a202c;
    text-decoration: none;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-left h3 a:hover { color: #3b82f6; }

.product-category {
    font-size: 0.7rem;
    color: #94a3b8;
    font-style: italic;
}

.product-right { text-align: right; }

.current-price {
    font-size: 1.1rem;
    font-weight: 700;
    color: #3b82f6;
    font-style: italic;
}

.original-price {
    font-size: 0.75rem;
 color: #94a3b8;
 text-decoration: line-through;
}

.stock-info {
    font-size: 0.7rem;
    color: #10b981;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 4px;
}

.stock-info.out-of-stock { color: #ef4444; }

.card-footer {
    padding: 0.5rem 1rem 1rem;
    margin-top: auto;
}

.action-buttons {
    display: flex;
    gap: 10px;
}

.add-to-cart-btn {
    flex: 1;
    padding: 0.7rem;
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: all 0.3s;
}

.add-to-cart-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(59,130,246,0.4);
}

.buy-now-btn {
    flex: 1;
    padding: 0.7rem;
    background: white;
    color: #3b82f6;
    border: 2px solid #3b82f6;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.8rem;
    text-align: center;
    transition: all 0.3s;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.buy-now-btn:hover {
    background: #3b82f6;
    color: white;
}

/* GRID LAYOUT - 4 ON DESKTOP, 1 ON MOBILE */
#popular-section,
#trending-section,
#new-section {
    display: grid;
    gap: 24px;
    padding: 0 16px;
}

/* Mobile: 1 card, full width */
@media (max-width: 639px) {
    #popular-section,
    #trending-section,
    #new-section {
        grid-template-columns: repeat(1, 1fr);
        gap: 20px;
        padding: 0 20px;
    }
    
    .card {
        max-width: 380px;
        margin: 0 auto;
        width: 100%;
    }
}

/* Tablet: 2-3 cards */
@media (min-width: 640px) and (max-width: 1023px) {
    #popular-section,
    #trending-section,
    #new-section {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* Desktop: 4 cards */
@media (min-width: 1024px) {
    #popular-section,
    #trending-section,
    #new-section {
        grid-template-columns: repeat(4, 1fr);
        max-width: 1400px;
        margin: 0 auto;
        padding: 0;
    }
    
    .card {
        max-width: none;
    }
}
</style>