@extends('layouts.app')

@section('content')
    <div class="checkout-wrapper">
        <div class="checkout-container">
            <div class="checkout-header">
                <h2>Checkout</h2>
                <div class="checkout-steps">
                    <div class="step active">
                        <div class="step-circle">1</div>
                        <span>Cart Review</span>
                    </div>
                    <div class="step-line"></div>
                    <div class="step active">
                        <div class="step-circle">2</div>
                        <span>Delivery</span>
                    </div>
                    <div class="step-line"></div>
                    <div class="step">
                        <div class="step-circle">3</div>
                        <span>Payment</span>
                    </div>
                </div>
            </div>

            @if (session('error'))
                <div class="checkout-message error">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="15" y1="9" x2="9" y2="15"></line>
                        <line x1="9" y1="9" x2="15" y2="15"></line>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            @if ($cartItems->isEmpty())
                <div class="empty-checkout">
                    <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#c0a062" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 30px;">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                    <p>Your cart is empty.</p>
                    <a href="{{ route('products.index') }}" class="continue-shopping-btn">Continue Shopping</a>
                </div>
            @else
                <div class="checkout-grid">
                    <!-- Order Summary (UNCHANGED) -->
                    <div class="order-summary-section">
                        <h3>Order Summary</h3>
                        <div class="order-items">
                            @foreach ($cartItems as $item)
                                @if ($item->product)
                                    <div class="order-item">
                                        <img 
                                            src="{{ $item->product->media->where('type', 'image')->first() ? asset('storage/' . $item->product->media->where('type', 'image')->first()->path) : 'https://via.placeholder.com/150' }}" 
                                            alt="{{ $item->product->name }}"
                                            class="order-item-image"
                                        >
                                        <div class="order-item-details">
                                            <h4>{{ $item->product->name }}</h4>
                                            <p>Qty: {{ $item->quantity }}</p>
                                        </div>
                                        <div class="order-item-price">
                                            <span>KSh {{ number_format($item->product->price * $item->quantity, 2) }}</span>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        <div class="order-total">
                            <div class="total-row subtotal">
                                <span>Subtotal</span>
                                <span>KSh {{ number_format($total, 2) }}</span>
                            </div>
                            <div class="total-row shipping">
                                <span>Shipping</span>
                                <span>Calculated at next step</span>
                            </div>
                            <div class="total-row grand-total">
                                <span>Total</span>
                                <span>KSh {{ number_format($total, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Form -->
                    <div class="delivery-form-section">
                        <h3>Delivery Address</h3>
                        <form method="POST" action="{{ route('orders.store') }}" class="checkout-form">
                            @csrf
                            
                            @auth
                                @if (!$addresses->isEmpty())
                                    <div class="form-group">
                                        <label for="address_id" class="form-label">Saved Addresses</label>
                                        <select name="address_id" id="address_id" class="form-select">
                                            <option value="">Enter a new address</option>
                                            @foreach ($addresses as $address)
                                                <option value="{{ $address->id }}">
                                                    {{ $address->name }} - {{ $address->line1 }}, {{ $address->city }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('address_id')
                                            <span class="error-message">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endif
                            @endauth

                            <!-- ✅ SINGLE NAME FIELD! -->
                            <div class="form-group full-width">
                                <label for="shipping_address.name" class="form-label">Full Name *</label>
                                <input 
                                    type="text" 
                                    name="shipping_address[name]" 
                                    id="shipping_address.name" 
                                    class="form-input @error('shipping_address.name') input-error @enderror" 
                                    value="{{ old('shipping_address.name') }}" 
                                    required
                                >
                                @error('shipping_address.name')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="shipping_address.email" class="form-label">Email *</label>
                                    <input 
                                        type="email" 
                                        name="shipping_address[email]" 
                                        id="shipping_address.email" 
                                        class="form-input @error('shipping_address.email') input-error @enderror" 
                                        value="{{ old('shipping_address.email', auth()->user()->email ?? '') }}" 
                                        required
                                    >
                                    @error('shipping_address.email')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="shipping_address.phone" class="form-label">Phone *</label>
                                    <input 
                                        type="text" 
                                        name="shipping_address[phone]" 
                                        id="shipping_address.phone" 
                                        class="form-input @error('shipping_address.phone') input-error @enderror" 
                                        value="{{ old('shipping_address.phone') }}" 
                                        required
                                    >
                                    @error('shipping_address.phone')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="shipping_address.line1" class="form-label">Address Line 1 *</label>
                                <input 
                                    type="text" 
                                    name="shipping_address[line1]" 
                                    id="shipping_address.line1" 
                                    class="form-input @error('shipping_address.line1') input-error @enderror" 
                                    value="{{ old('shipping_address.line1') }}" 
                                    required
                                >
                                @error('shipping_address.line1')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="shipping_address.line2" class="form-label">Address Line 2</label>
                                <input 
                                    type="text" 
                                    name="shipping_address[line2]" 
                                    id="shipping_address.line2" 
                                    class="form-input" 
                                    value="{{ old('shipping_address.line2') }}"
                                >
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="shipping_address.city" class="form-label">City *</label>
                                    <input 
                                        type="text" 
                                        name="shipping_address[city]" 
                                        id="shipping_address.city" 
                                        class="form-input @error('shipping_address.city') input-error @enderror" 
                                        value="{{ old('shipping_address.city', 'Nairobi') }}" 
                                        required
                                    >
                                    @error('shipping_address.city')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="shipping_address.state" class="form-label">State</label>
                                    <input 
                                        type="text" 
                                        name="shipping_address[state]" 
                                        id="shipping_address.state" 
                                        class="form-input" 
                                        value="{{ old('shipping_address.state', 'Nairobi') }}"
                                    >
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="shipping_address.postal_code" class="form-label">Postal Code</label>
                                    <input 
                                        type="text" 
                                        name="shipping_address[postal_code]" 
                                        id="shipping_address.postal_code" 
                                        class="form-input" 
                                        value="{{ old('shipping_address.postal_code') }}"
                                    >
                                </div>

                                <div class="form-group">
                                    <label for="shipping_address.country" class="form-label">Country *</label>
                                    <input 
                                        type="text" 
                                        name="shipping_address[country]" 
                                        id="shipping_address.country" 
                                        class="form-input @error('shipping_address.country') input-error @enderror" 
                                        value="{{ old('shipping_address.country', 'Kenya') }}" 
                                        required
                                    >
                                    @error('shipping_address.country')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="billing-checkbox">
                                <input type="checkbox" name="use_billing" id="use_billing">
                                <label for="use_billing">Use a different billing address</label>
                            </div>

                            <div id="billing-address" class="billing-section hidden">
                                <h3>Billing Address</h3>
                                
                                <!-- ✅ SINGLE BILLING NAME! -->
                                <div class="form-group full-width">
                                    <label for="billing_address.name" class="form-label">Full Name</label>
                                    <input type="text" name="billing_address[name]" id="billing_address.name" class="form-input" value="{{ old('billing_address.name') }}">
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="billing_address.email" class="form-label">Email</label>
                                        <input type="email" name="billing_address[email]" id="billing_address.email" class="form-input" value="{{ old('billing_address.email') }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="billing_address.phone" class="form-label">Phone</label>
                                        <input type="text" name="billing_address[phone]" id="billing_address.phone" class="form-input" value="{{ old('billing_address.phone') }}">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="billing_address.line1" class="form-label">Address Line 1</label>
                                    <input type="text" name="billing_address[line1]" id="billing_address.line1" class="form-input" value="{{ old('billing_address.line1') }}">
                                </div>

                                <div class="form-group">
                                    <label for="billing_address.line2" class="form-label">Address Line 2</label>
                                    <input type="text" name="billing_address[line2]" id="billing_address.line2" class="form-input" value="{{ old('billing_address.line2') }}">
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="billing_address.city" class="form-label">City</label>
                                        <input type="text" name="billing_address[city]" id="billing_address.city" class="form-input" value="{{ old('billing_address.city') }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="billing_address.state" class="form-label">State</label>
                                        <input type="text" name="billing_address[state]" id="billing_address.state" class="form-input" value="{{ old('billing_address.state') }}">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="billing_address.postal_code" class="form-label">Postal Code</label>
                                        <input type="text" name="billing_address[postal_code]" id="billing_address.postal_code" class="form-input" value="{{ old('billing_address.postal_code') }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="billing_address.country" class="form-label">Country</label>
                                        <input type="text" name="billing_address[country]" id="billing_address.country" class="form-input" value="{{ old('billing_address.country') }}">
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="total" value="{{ $total }}">
                            
                            <button type="submit" class="place-order-btn">
                                <span>Place Order</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 12h14"></path>
                                    <path d="m12 5 7 7-7 7"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.getElementById('use_billing')?.addEventListener('change', function () {
            document.getElementById('billing-address').classList.toggle('hidden', !this.checked);
        });
        
        document.getElementById('address_id')?.addEventListener('change', function () {
            const inputs = document.querySelectorAll('[name^="shipping_address"]');
            inputs.forEach(input => input.required = !this.value);
        });
    </script>
@endsection