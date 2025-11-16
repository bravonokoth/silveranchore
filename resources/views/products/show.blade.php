@extends('layouts.app')

@section('content')
@php
    use Illuminate\Support\Facades\Storage;
@endphp

<div class="product-show-wrapper">
    <div class="product-show-container">
        <!-- Breadcrumb -->
        <div class="breadcrumb-nav">
            <a href="{{ route('home') }}">Home</a>
            <span>/</span>
            <a href="{{ route('products.index') }}">Products</a>
            <span>/</span>
            @if($product->category)
                <a href="{{ route('categories.show', $product->category) }}">{{ $product->category->name }}</a>
                <span>/</span>
            @endif
            <span>{{ $product->name }}</span>
        </div>

        <!-- Product Main Section -->
        <div class="product-main-grid">
            <!-- Product Image -->
            <div class="product-image-area">
                <div class="product-main-image">
                    @php
                        $imagePath = $product->media->first()?->path ?? 'images/placeholder.jpg';
                        $fullImagePath = $product->media->first() ? Storage::url($imagePath) : asset($imagePath);
                    @endphp
                    <img 
                        src="{{ $fullImagePath }}" 
                        alt="{{ $product->name }}"
                        onerror="this.src='{{ asset('images/placeholder.jpg') }}'"
                        loading="lazy"
                    >
                    @if($product->stock <= 0)
                        <span class="stock-label out-stock">Out of Stock</span>
                    @else
                        <span class="stock-label in-stock">In Stock</span>
                    @endif
                </div>
            </div>

            <!-- Product Info -->
            <div class="product-info-area">
                <h1 class="product-main-title">{{ $product->name }}</h1>
                <div class="product-price-display">
                    <span class="price-amount">KSh {{ number_format($product->price, 2) }}</span>
                </div>
                <div class="product-short-desc">
                    <p>{{ Str::limit($product->description, 150) }}</p>
                </div>
                <div class="stock-status">
                    @if($product->stock > 0)
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span class="in-stock-text">In stock</span>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="15" y1="9" x2="9" y2="15"></line>
                            <line x1="9" y1="9" x2="15" y2="15"></line>
                        </svg>
                        <span class="out-stock-text">Out of stock</span>
                    @endif
                </div>

       @if ($product->stock > 0)
    <!-- Add to Cart Form -->
    <form action="{{ route('cart.store') }}" method="POST" class="product-cart-form">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        <div class="quantity-row">
            <div class="quantity-box">
                <button type="button" class="qty-decrease" onclick="decreaseQty()">−</button>
                <input 
                    type="number" 
                    name="quantity" 
                    id="quantity" 
                    value="1" 
                    min="1" 
                    max="{{ $product->stock }}" 
                    class="qty-input"
                    readonly
                >
                <button type="button" class="qty-increase" onclick="increaseQty({{ $product->stock }})">+</button>
            </div>
            <button type="submit" class="add-basket-btn">
                <i class="fas fa-shopping-cart"></i>
                Add to Cart
            </button>
            <a href="{{ route('checkout.quick', $product->id) }}" class="buy-now-btn-detail">
                <i class="fas fa-bolt"></i>
                Buy Now
            </a>
        </div>
        @error('quantity')
            <span class="error-text">{{ $message }}</span>
        @enderror
    </form>

                    <!-- Wishlist Button -->
                    <button 
                        type="button" 
                        class="wishlist-btn {{ $product->isInWishlist() ? 'active' : '' }}"
                        onclick="toggleWishlist({{ $product->id }})"
                        data-product-id="{{ $product->id }}"
                        data-in-wishlist="{{ $product->isInWishlist() ? 'true' : 'false' }}"
                    >
                        <svg class="wishlist-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                        </svg>
                        <span class="wishlist-text">
                            {{ $product->isInWishlist() ? 'In Wishlist' : 'Add to wishlist' }}
                        </span>
                    </button>
                @else
                    <div class="out-stock-alert">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="15" y1="9" x2="9" y2="15"></line>
                            <line x1="9" y1="9" x2="15" y2="15"></line>
                        </svg>
                        <span>This product is currently out of stock</span>
                    </div>
                @endif

                <div class="delivery-info-box">
                    <h4>24/7 Express Delivery</h4>
                    <p>
                        - Delivery in 20-50 minutes within Nairobi<br>
                        - Next-day country-wide delivery (order before 4pm)
                    </p>
                </div>

                <!-- Social Share -->
                <div class="social-share">
                    <span>Share:</span>
                    <a href="#" class="share-link facebook" title="Facebook">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="#3b5998">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                    <a href="#" class="share-link twitter" title="Twitter">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="#1DA1F2">
                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                        </svg>
                    </a>
                    <a href="#" class="share-link whatsapp" title="WhatsApp">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="#25D366">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                        </svg>
                    </a>
                    <a href="#" class="share-link copy" title="Copy Link" onclick="copyLink()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                            <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Product Tabs -->
        <div class="product-tabs-section">
            <div class="tabs-nav">
                <button class="tab-btn active" data-tab="description">Description</button>
                <button class="tab-btn" data-tab="additional">Additional Information</button>
                <button class="tab-btn" data-tab="reviews">Reviews</button>
            </div>

            <div class="tabs-content">
                <div class="tab-pane active" id="description">
                    <h3>Product Description</h3>
                    <p>{{ $product->description }}</p>
                </div>

                <div class="tab-pane" id="additional">
                    <h3>Additional Information</h3>
                    <table class="info-table">
                        <tr>
                            <th>Category</th>
                            <td>{{ $product->category->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Stock</th>
                            <td>{{ $product->stock }} units</td>
                        </tr>
                        <tr>
                            <th>Price</th>
                            <td>KSh {{ number_format($product->price, 2) }}</td>
                        </tr>
                    </table>
                </div>

                <div class="tab-pane" id="reviews">
                    <h3>Customer Reviews</h3>
                    <p class="no-reviews">No reviews yet. Be the first to review this product!</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function increaseQty(max) {
    const input = document.getElementById('quantity');
    const currentValue = parseInt(input.value);
    if (currentValue < max) {
        input.value = currentValue + 1;
    }
}

function decreaseQty() {
    const input = document.getElementById('quantity');
    const currentValue = parseInt(input.value);
    if (currentValue > 1) {
        input.value = currentValue - 1;
    }
}

async function toggleWishlist(productId) {
    const button = event.target.closest('.wishlist-btn');
    const isInWishlist = button.dataset.inWishlist === 'true';
    const url = isInWishlist ? `/wishlist/${productId}` : `/wishlist/store`;

    try {
        const response = await fetch(url, {
            method: isInWishlist ? 'DELETE' : 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ product_id: productId })
        });

        const data = await response.json();

        if (data.success) {
            button.classList.toggle('active');
            button.dataset.inWishlist = (!isInWishlist).toString();
            const text = button.querySelector('.wishlist-text');
            text.textContent = data.in_wishlist ? 'In Wishlist' : 'Add to wishlist';
            showNotification(data.message);
        }
    } catch (error) {
        console.error('Wishlist error:', error);
        showNotification('Something went wrong!');
    }
}

function copyLink() {
    navigator.clipboard.writeText(window.location.href);
    showNotification('Link copied!');
}

function showNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.textContent = message;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 2000);
}

document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const tab = btn.dataset.tab;
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById(tab).classList.add('active');
    });
});
</script>

<style>
.wishlist-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    border: 2px solid #e0e0e0;
    background: white;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    width: 100%;
    justify-content: center;
    margin-top: 10px;
}

.wishlist-btn:hover {
    border-color: #ff6b6b;
}

.wishlist-btn.active {
    border-color: #ff6b6b;
    background: #fff5f5;
    color: #ff6b6b;
}

.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 12px 20px;
    background: #4caf50;
    color: white;
    border-radius: 6px;
    z-index: 1000;
    animation: slideIn 0.3s ease;
}

/* Product Detail Page - Buy Now Button */
.quantity-row {
    display: flex;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
}

.buy-now-btn-detail {
    padding: 12px 30px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border: none;
    color: white;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 600;
    font-size: 15px;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.buy-now-btn-detail:hover {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.6);
    transform: translateY(-2px);
}

.buy-now-btn-detail i {
    font-size: 14px;
}

.add-basket-btn {
    padding: 12px 30px;
    background: linear-gradient(135deg, #8b5cf6 0%, #ec4899 100%);
    border: none;
    color: white;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 600;
    font-size: 15px;
    box-shadow: 0 4px 15px rgba(139, 92, 246, 0.4);
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.add-basket-btn:hover {
    background: linear-gradient(135deg, #7c3aed 0%, #db2777 100%);
    box-shadow: 0 6px 20px rgba(139, 92, 246, 0.6);
    transform: translateY(-2px);
}

.add-basket-btn i {
    font-size: 14px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .quantity-row {
        flex-direction: column;
        align-items: stretch;
    }

    .quantity-box {
        width: 100%;
    }

    .add-basket-btn,
    .buy-now-btn-detail {
        width: 100%;
        justify-content: center;
    }
}

@keyframes slideIn {
    from { transform: translateX(100%); }
    to { transform: translateX(0); }
}
</style>
@endsection