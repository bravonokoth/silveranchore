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

            @if (session('success'))
                <div class="checkout-message success">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    {{ session('success') }}
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
                    <!-- Order Summary -->
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
                        <form method="POST" action="{{ route('orders.store') }}" class="checkout-form" id="checkout-form">
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

                            <!-- Full Name Field -->
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
                                <input type="checkbox" name="use_billing" id="use_billing" value="1">
                                <label for="use_billing">Use a different billing address</label>
                            </div>

                            <div id="billing-address" class="billing-section hidden">
                                <h3>Billing Address</h3>
                                
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

                            <!-- Payment Method Selection -->
                            <div class="payment-method-section">
                                <h3>Select Payment Method</h3>
                                <div class="payment-methods">
                                    <label class="payment-option">
                                        <input type="radio" name="payment_method" value="paystack" checked>
                                        <div class="payment-card paystack-card">
                                            <div class="payment-logo">
                                                <svg width="100" height="30" viewBox="0 0 120 30" fill="none">
                                                    <rect width="120" height="30" fill="#00C3F7" rx="4"/>
                                                    <text x="60" y="20" font-family="Arial, sans-serif" font-size="14" font-weight="bold" fill="white" text-anchor="middle">PAYSTACK</text>
                                                </svg>
                                            </div>
                                            <p>Pay with Card, Bank Transfer, or USSD</p>
                                            <span class="payment-badge">Recommended</span>
                                        </div>
                                    </label>

                                    <label class="payment-option">
                                        <input type="radio" name="payment_method" value="pesapal">
                                        <div class="payment-card pesapal-card">
                                            <div class="payment-logo">
                                                <svg width="100" height="30" viewBox="0 0 120 30" fill="none">
                                                    <rect width="120" height="30" fill="#00C851" rx="4"/>
                                                    <text x="60" y="20" font-family="Arial, sans-serif" font-size="14" font-weight="bold" fill="white" text-anchor="middle">PESAPAL</text>
                                                </svg>
                                            </div>
                                            <p>Mobile Money & Card Payments</p>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <input type="hidden" name="total" value="{{ $total }}">
                            
                            <!-- Dynamic Payment Button -->
                            <button type="submit" class="place-order-btn payment-btn paystack-btn" id="payment-btn">
                                <span id="btn-text">
                                    💳 Pay KSh {{ number_format($total, 2) }} with Paystack
                                </span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M5 12h14M12 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        // Toggle billing address section
        const useBillingCheckbox = document.getElementById('use_billing');
        const billingSection = document.getElementById('billing-address');
        
        useBillingCheckbox?.addEventListener('change', function () {
            billingSection.classList.toggle('hidden', !this.checked);
            
            // ✅ Clear billing inputs when unchecked to prevent null validation errors
            if (!this.checked) {
                billingSection.querySelectorAll('input').forEach(input => {
                    input.value = '';
                });
            }
        });
        
        // Handle saved address selection
        document.getElementById('address_id')?.addEventListener('change', function () {
            const inputs = document.querySelectorAll('[name^="shipping_address"]');
            inputs.forEach(input => input.required = !this.value);
        });

        // ✅ CRITICAL: Remove empty billing fields before form submission
        document.getElementById('checkout-form')?.addEventListener('submit', function(e) {
            const useBilling = document.getElementById('use_billing');
            
            // If billing checkbox is NOT checked, remove all billing fields from submission
            if (!useBilling || !useBilling.checked) {
                document.querySelectorAll('[name^="billing_address"]').forEach(input => {
                    input.removeAttribute('name'); // Removes from form data entirely
                });
            }
        });

        // Dynamic payment button text and styling
        const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
        const paymentBtn = document.getElementById('payment-btn');
        const btnText = document.getElementById('btn-text');
        const total = "{{ number_format($total, 2) }}";

        paymentRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'paystack') {
                    btnText.textContent = `💳 Pay KSh ${total} with Paystack`;
                    paymentBtn.className = 'place-order-btn payment-btn paystack-btn';
                } else if (this.value === 'pesapal') {
                    btnText.textContent = `📱 Pay KSh ${total} with PesaPal`;
                    paymentBtn.className = 'place-order-btn payment-btn pesapal-btn';
                }
            });
        });
    </script>

    <style>
        .checkout-wrapper {
    background: linear-gradient(135deg, #f0f4ff 0%, #e8f4f8 100%);
    min-height: 100vh;
    padding: 40px 20px;
}

.checkout-container {
    max-width: 1400px;
    margin: 0 auto;
}

.checkout-header {
    margin-bottom: 40px;
}

.checkout-header h2 {
    font-size: 32px;
    font-weight: 600;
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 50%, #ec4899 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 30px;
}

/* Checkout Steps */
.checkout-steps {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 20px;
    padding: 30px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 15px rgba(99, 102, 241, 0.1);
    border: 1px solid rgba(147, 197, 253, 0.3);
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
}

.step-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(147, 197, 253, 0.2);
    color: #64748b;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    border: 2px solid rgba(147, 197, 253, 0.3);
    transition: all 0.3s ease;
}

.step.active .step-circle {
    background: linear-gradient(135deg, #60a5fa 0%, #a78bfa 100%);
    color: white;
    border-color: transparent;
    box-shadow: 0 4px 12px rgba(96, 165, 250, 0.4);
}

.step span {
    font-size: 14px;
    color: #64748b;
    font-weight: 500;
}

.step.active span {
    color: #3b82f6;
    font-weight: 600;
}

.step-line {
    width: 80px;
    height: 2px;
    background: rgba(147, 197, 253, 0.3);
}

/* Checkout Grid */
.checkout-grid {
    display: grid;
    grid-template-columns: 1fr 1.2fr;
    gap: 30px;
}

/* Order Summary Section */
.order-summary-section {
    background: white;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 4px 15px rgba(99, 102, 241, 0.1);
    border: 1px solid rgba(147, 197, 253, 0.3);
    height: fit-content;
    position: sticky;
    top: 20px;
}

.order-summary-section h3 {
    font-size: 24px;
    font-weight: 600;
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 20px;
}

.order-items {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-bottom: 20px;
    max-height: 400px;
    overflow-y: auto;
}

.order-item {
    display: flex;
    gap: 15px;
    padding: 15px;
    background: linear-gradient(135deg, rgba(96, 165, 250, 0.05) 0%, rgba(168, 85, 247, 0.05) 100%);
    border-radius: 12px;
    border: 1px solid rgba(147, 197, 253, 0.2);
}

.order-item-image {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
    border: 2px solid rgba(147, 197, 253, 0.3);
}

.order-item-details {
    flex: 1;
}

.order-item-details h4 {
    font-size: 16px;
    color: #1e293b;
    margin-bottom: 5px;
    font-weight: 600;
}

.order-item-details p {
    font-size: 14px;
    color: #64748b;
}

.order-item-price {
    display: flex;
    align-items: center;
}

.order-item-price span {
    font-size: 18px;
    font-weight: 600;
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.order-total {
    border-top: 2px solid rgba(147, 197, 253, 0.3);
    padding-top: 20px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.total-row {
    display: flex;
    justify-content: space-between;
    font-size: 16px;
}

.total-row.subtotal,
.total-row.shipping {
    color: #64748b;
}

.total-row.grand-total {
    font-size: 20px;
    font-weight: 600;
    padding-top: 12px;
    border-top: 2px solid rgba(147, 197, 253, 0.3);
}

.total-row.grand-total span {
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Delivery Form Section */
.delivery-form-section {
    background: white;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 4px 15px rgba(99, 102, 241, 0.1);
    border: 1px solid rgba(147, 197, 253, 0.3);
}

.delivery-form-section h3 {
    font-size: 24px;
    font-weight: 600;
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 25px;
}

/* Form Styling */
.checkout-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-label {
    font-size: 14px;
    font-weight: 600;
    color: #1e293b;
}

.form-input,
.form-select {
    padding: 12px 16px;
    border: 2px solid rgba(147, 197, 253, 0.5);
    border-radius: 12px;
    font-size: 15px;
    color: #1e293b;
    background: white;
    transition: all 0.3s ease;
}

.form-input:focus,
.form-select:focus {
    outline: none;
    border-color: #8b5cf6;
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}

.form-input::placeholder {
    color: #94a3b8;
}

.input-error {
    border-color: #ec4899;
}

.error-message {
    color: #ec4899;
    font-size: 13px;
    font-weight: 500;
}

/* Billing Checkbox */
.billing-checkbox {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 15px;
    background: linear-gradient(135deg, rgba(96, 165, 250, 0.05) 0%, rgba(168, 85, 247, 0.05) 100%);
    border-radius: 12px;
    border: 1px solid rgba(147, 197, 253, 0.2);
}

.billing-checkbox input[type="checkbox"] {
    width: 20px;
    height: 20px;
    cursor: pointer;
    accent-color: #8b5cf6;
}

.billing-checkbox label {
    font-size: 15px;
    color: #1e293b;
    cursor: pointer;
    font-weight: 500;
}

/* Billing Section */
.billing-section {
    margin-top: 20px;
    padding: 25px;
    background: linear-gradient(135deg, rgba(96, 165, 250, 0.05) 0%, rgba(168, 85, 247, 0.05) 100%);
    border-radius: 12px;
    border: 1px solid rgba(147, 197, 253, 0.3);
}

.billing-section h3 {
    font-size: 20px;
    font-weight: 600;
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 20px;
}

/* Payment Method Section */
.payment-method-section {
    margin-top: 30px;
    padding-top: 30px;
    border-top: 2px solid rgba(147, 197, 253, 0.3);
}

.payment-method-section h3 {
    font-size: 20px;
    font-weight: 600;
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 20px;
}

.payment-methods {
    display: grid;
    gap: 15px;
}

.payment-option {
    cursor: pointer;
    position: relative;
}

.payment-option input[type="radio"] {
    position: absolute;
    opacity: 0;
}

.payment-card {
    border: 2px solid rgba(147, 197, 253, 0.3);
    border-radius: 12px;
    padding: 20px;
    transition: all 0.3s ease;
    background: white;
    position: relative;
}

.payment-option input[type="radio"]:checked + .payment-card {
    border-color: #60a5fa;
    box-shadow: 0 4px 12px rgba(96, 165, 250, 0.3);
    background: linear-gradient(135deg, rgba(96, 165, 250, 0.05) 0%, rgba(168, 85, 247, 0.05) 100%);
}

.payment-option input[type="radio"]:checked + .pesapal-card {
    border-color: #8b5cf6;
    box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
}

.payment-card:hover {
    border-color: #93c5fd;
    transform: translateY(-2px);
}

.payment-logo {
    margin-bottom: 10px;
}

.payment-card p {
    color: #64748b;
    font-size: 14px;
    margin: 0;
}

.payment-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: linear-gradient(135deg, #60a5fa 0%, #a78bfa 100%);
    color: white;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(96, 165, 250, 0.3);
}

/* Place Order Button */
.place-order-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 16px 32px;
    font-size: 18px;
    font-weight: 600;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    width: 100%;
    margin-top: 20px;
    color: white;
    border: none;
}

.paystack-btn {
    background: linear-gradient(135deg, #60a5fa 0%, #a78bfa 100%);
    box-shadow: 0 4px 15px rgba(96, 165, 250, 0.4);
}

.paystack-btn:hover {
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(96, 165, 250, 0.6);
}

.pesapal-btn {
    background: linear-gradient(135deg, #8b5cf6 0%, #ec4899 100%);
    box-shadow: 0 4px 15px rgba(139, 92, 246, 0.4);
}

.pesapal-btn:hover {
    background: linear-gradient(135deg, #7c3aed 0%, #db2777 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(139, 92, 246, 0.6);
}

.place-order-btn svg {
    transition: transform 0.3s ease;
}

.place-order-btn:hover svg {
    transform: translateX(5px);
}

/* Messages */
.checkout-message {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 15px 20px;
    border-radius: 12px;
    margin-bottom: 20px;
    font-weight: 500;
    border: 2px solid;
}

.checkout-message.success {
    background: linear-gradient(135deg, rgba(96, 165, 250, 0.1) 0%, rgba(168, 85, 247, 0.1) 100%);
    color: #3b82f6;
    border-color: rgba(96, 165, 250, 0.3);
}

.checkout-message.error {
    background: linear-gradient(135deg, rgba(236, 72, 153, 0.1) 0%, rgba(244, 114, 182, 0.1) 100%);
    color: #ec4899;
    border-color: rgba(236, 72, 153, 0.3);
}

/* Empty Checkout */
.empty-checkout {
    text-align: center;
    padding: 80px 20px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 15px rgba(99, 102, 241, 0.1);
    border: 1px solid rgba(147, 197, 253, 0.3);
}

.empty-checkout p {
    font-size: 18px;
    color: #64748b;
    margin-bottom: 30px;
}

.continue-shopping-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 28px;
    background: linear-gradient(135deg, #8b5cf6 0%, #ec4899 100%);
    color: white;
    text-decoration: none;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(139, 92, 246, 0.4);
}

.continue-shopping-btn:hover {
    background: linear-gradient(135deg, #7c3aed 0%, #db2777 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(139, 92, 246, 0.6);
}
    </style>
@endsection