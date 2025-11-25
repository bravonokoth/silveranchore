{{-- resources/views/partials/product-card.blade.php --}}
<div class="product-card-grid">
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
                    <button onclick="openCartSidebar()" class="add-to-cart-btn">
                                Add to Cart
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
/* SAFE PRODUCT CARD FOR GRID — UPDATED 2025 VERSION */
.product-card-grid {
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(74, 144, 226, 0.12);
    transition: all 0.3s ease;
    height: 440px;
    display: flex;
    flex-direction: column;
}

.product-card-grid:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 35px rgba(74, 144, 226, 0.25);
}

.product-card-grid .position-relative {
    position: relative;
    height: 270px;
    background: #f8f9ff;
    overflow: hidden;
}

.product-card-grid .card-img-top {
    width: 100%;
    height: 270px;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.product-card-grid:hover .card-img-top {
    transform: scale(1.08);
}

.product-card-grid .card-body {
    padding: 1rem;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.5rem;
    flex-grow: 1;
}

.product-card-grid h3 {
    font-size: 0.95rem;
    font-weight: 600;
    margin: 0;
    line-height: 1.2;
}

.product-card-grid h3 a {
    color: #1a202c;
    text-decoration: none;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-card-grid .product-category {
    font-size: 0.7rem;
    color: #94a3b8;
    font-style: italic;
}

.product-card-grid .current-price {
    font-size: 1.1rem;
    font-weight: 700;
    color: #3b82f6;
}

.product-card-grid .original-price {
    font-size: 0.75rem;
    color: #94a3b8;
    text-decoration: line-through;
}

.product-card-grid .stock-info {
    font-size: 0.7rem;
    color: #10b981;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 4px;
}

.product-card-grid .card-footer {
    padding: 0.5rem 1rem 1rem;
    margin-top: auto;
}

.product-card-grid .action-buttons {
    display: flex;
    gap: 10px;
}

.product-card-grid .add-to-cart-btn,
.product-card-grid .buy-now-btn {
    flex: 1;
    padding: 0.75rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.82rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: all 0.3s;
}

.product-card-grid .add-to-cart-btn {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
    border: none;
}

.product-card-grid .buy-now-btn {
    background: rgb(62, 226, 62);
    color: #3b82f6;
    border: 2px solid #3b82f6;
}

/* ============================================= */
/* NEW: 2 CARDS ON MOBILE + PRICE TO FAR RIGHT   */
/* ============================================= */

/* 2 cards on mobile (replaces old 1-card rule) */
@media (max-width: 767px) {
    .product-card-grid {
        height: 410px;
        margin: 0;
        max-width: none;
    }
    .product-card-grid .position-relative,
    .product-card-grid .card-img-top {
        height: 220px;
    }
}

/* Push price & stock to far right on tablet+ */
@media (min-width: 640px) {
    .product-card-grid .card-body {
        grid-template-columns: 1fr auto !important;
        gap: 12px;
        align-items: start;
    }
    .product-card-grid .product-right {
        text-align: right;
    }
    .product-card-grid .price-info,
    .product-card-grid .stock-info {
        justify-content: flex-end;
    }
    .product-card-grid .current-price {
        font-size: 1.2rem;
        font-weight: 800;
    }
}

/* GRID LAYOUT — ALL SECTIONS (Homepage + Products Index) */
#popular-section,
#trending-section,
#new-section,
#products-grid-container {
    display: grid;
    gap: 20px;
    padding: 0 12px;
}

/* Mobile: 2 columns */
@media (max-width: 767px) {
    #popular-section,
    #trending-section,
    #new-section,
    #products-grid-container {
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        padding: 0 12px;
    }
}

/* Tablet: 3 columns */
@media (min-width: 768px) and (max-width: 1023px) {
    #popular-section,
    #trending-section,
    #new-section,
    #products-grid-container {
        grid-template-columns: repeat(3, 1fr);
    }
}

/* Desktop: 4 columns */
@media (min-width: 1024px) {
    #popular-section,
    #trending-section,
    #new-section,
    #products-grid-container {
        grid-template-columns: repeat(4, 1fr);
        max-width: 1400px;
        margin: 0 auto;
        padding: 0;
        gap: 24px;
    }
}
</style>