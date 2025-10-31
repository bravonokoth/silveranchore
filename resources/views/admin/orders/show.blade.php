@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mt-4">
                <i class="fas fa-receipt"></i> Order #{{ $order->id }}
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Orders</a></li>
                    <li class="breadcrumb-item active">Order #{{ $order->id }}</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group" role="group">
            <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit Order
            </a>
            @if($order->status !== 'canceled' && $order->status !== 'delivered')
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                    <i class="fas fa-ban"></i> Cancel Order
                </button>
            @endif
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Orders
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Left Column: Order Details -->
        <div class="col-lg-8">
            <!-- Order Items -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-shopping-bag"></i> Order Items</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 60%;">Product</th>
                                    <th style="width: 15%;">Quantity</th>
                                    <th style="width: 15%;">Unit Price</th>
                                    <th style="width: 10%;">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($order->items as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($item->product && $item->product->media->first())
                                                    <img src="{{ asset('storage/' . $item->product->media->first()->path) }}" 
                                                         alt="{{ $item->product->name }}" 
                                                         class="rounded me-3"
                                                         style="width: 60px; height: 60px; object-fit: cover;">
                                                @else
                                                    <div class="bg-secondary rounded me-3 d-flex align-items-center justify-content-center" 
                                                         style="width: 60px; height: 60px;">
                                                        <i class="fas fa-image text-white"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <strong>{{ $item->product->name ?? 'Product Deleted' }}</strong>
                                                    @if($item->product)
                                                        <br>
                                                        <small class="text-muted">SKU: {{ $item->product->sku }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <span class="badge bg-secondary fs-6">{{ $item->quantity }}</span>
                                        </td>
                                        <td class="align-middle">KSh {{ number_format($item->price, 2) }}</td>
                                        <td class="align-middle">
                                            <strong>KSh {{ number_format($item->price * $item->quantity, 2) }}</strong>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                            No items found in this order.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Total:</td>
                                    <td class="fw-bold text-success fs-5">
                                        KSh {{ number_format($order->total, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-shipping-fast"></i> Shipping Address</h5>
                </div>
                <div class="card-body">
                    @if($order->shippingAddress)
                        <address class="mb-0">
                            <strong>{{ $order->shippingAddress->name }}</strong><br>
                            {{ $order->shippingAddress->line1 }}<br>
                            @if($order->shippingAddress->line2)
                                {{ $order->shippingAddress->line2 }}<br>
                            @endif
                            {{ $order->shippingAddress->city }}@if($order->shippingAddress->state), {{ $order->shippingAddress->state }}@endif
                            @if($order->shippingAddress->postal_code) {{ $order->shippingAddress->postal_code }}@endif<br>
                            {{ $order->shippingAddress->country }}<br>
                            <i class="fas fa-phone"></i> <strong>Phone:</strong> {{ $order->shippingAddress->phone_number }}<br>
                            <i class="fas fa-envelope"></i> <strong>Email:</strong> {{ $order->shippingAddress->email }}
                        </address>
                    @else
                        <p class="text-muted mb-0">
                            <i class="fas fa-exclamation-triangle"></i> No shipping address provided.
                        </p>
                    @endif
                </div>
            </div>

            <!-- Billing Address (if different) -->
            @if($order->billingAddress && $order->billing_address_id !== $order->shipping_address_id)
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0"><i class="fas fa-file-invoice"></i> Billing Address</h5>
                    </div>
                    <div class="card-body">
                        <address class="mb-0">
                            <strong>{{ $order->billingAddress->name }}</strong><br>
                            {{ $order->billingAddress->line1 }}<br>
                            @if($order->billingAddress->line2)
                                {{ $order->billingAddress->line2 }}<br>
                            @endif
                            {{ $order->billingAddress->city }}@if($order->billingAddress->state), {{ $order->billingAddress->state }}@endif
                            @if($order->billingAddress->postal_code) {{ $order->billingAddress->postal_code }}@endif<br>
                            {{ $order->billingAddress->country }}<br>
                            <i class="fas fa-phone"></i> <strong>Phone:</strong> {{ $order->billingAddress->phone_number }}<br>
                            <i class="fas fa-envelope"></i> <strong>Email:</strong> {{ $order->billingAddress->email }}
                        </address>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column: Order Summary & Customer Info -->
        <div class="col-lg-4">
            <!-- Order Summary -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Order Summary</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <strong>Order ID:</strong>
                            <span class="float-end">#{{ $order->id }}</span>
                        </li>
                        <li class="mb-3">
                            <strong>Created:</strong>
                            <span class="float-end">{{ $order->created_at->format('M d, Y H:i') }}</span>
                        </li>
                        <li class="mb-3">
                            <strong>Last Updated:</strong>
                            <span class="float-end">{{ $order->updated_at->format('M d, Y H:i') }}</span>
                        </li>
                        <li class="mb-3">
                            <strong>Order Status:</strong>
                            <span class="float-end">
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'processing' => 'info',
                                        'shipped' => 'primary',
                                        'delivered' => 'success',
                                        'canceled' => 'danger'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </span>
                        </li>
                        <li class="mb-3">
                            <strong>Payment Status:</strong>
                            <span class="float-end">
                                @php
                                    $paymentColors = [
                                        'pending' => 'warning',
                                        'paid' => 'success',
                                        'failed' => 'danger',
                                        'refunded' => 'info'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $paymentColors[$order->payment_status] ?? 'secondary' }}">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </span>
                        </li>
                        <li class="mb-3">
                            <strong>Payment Method:</strong>
                            <span class="float-end">
                                <span class="badge bg-dark">
                                    {{ ucfirst($order->payment_method ?? 'N/A') }}
                                </span>
                            </span>
                        </li>
                        @if($order->payment_reference)
                            <li class="mb-3">
                                <strong>Payment Ref:</strong>
                                <span class="float-end">
                                    <code>{{ Str::limit($order->payment_reference, 15) }}</code>
                                </span>
                            </li>
                        @endif
                        @if($order->paid_at)
                            <li class="mb-0">
                                <strong>Paid At:</strong>
                                <span class="float-end">{{ $order->paid_at->format('M d, Y H:i') }}</span>
                            </li>
                        @endif
                    </ul>
                </div>
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <strong class="fs-5">Total Amount:</strong>
                        <strong class="text-success fs-4">KSh {{ number_format($order->total, 2) }}</strong>
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user"></i> Customer Information</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <strong>Name:</strong><br>
                            @if($order->user)
                                <i class="fas fa-user-check text-success"></i> {{ $order->user->name }}
                                <span class="badge bg-success">Registered</span>
                            @elseif($order->shippingAddress)
                                <i class="fas fa-user-circle text-secondary"></i> {{ $order->shippingAddress->name }}
                                <span class="badge bg-secondary">Guest</span>
                            @else
                                <span class="text-muted">Unknown</span>
                            @endif
                        </li>
                        <li class="mb-3">
                            <strong>Email:</strong><br>
                            <a href="mailto:{{ $order->email }}">
                                <i class="fas fa-envelope"></i> {{ $order->email }}
                            </a>
                        </li>
                        <li class="mb-0">
                            <strong>Phone:</strong><br>
                            @if($order->shippingAddress && $order->shippingAddress->phone_number)
                                <a href="tel:{{ $order->shippingAddress->phone_number }}">
                                    <i class="fas fa-phone"></i> {{ $order->shippingAddress->phone_number }}
                                </a>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Order Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="cancelModalLabel">
                    <i class="fas fa-ban"></i> Cancel Order #{{ $order->id }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2">Are you sure you want to cancel this order?</p>
                <p class="text-muted mb-0">
                    <small><i class="fas fa-info-circle"></i> The customer will be notified about the cancellation.</small>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    No, Keep Order
                </button>
                <form action="{{ route('admin.orders.drop', $order) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-ban"></i> Yes, Cancel Order
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Ensure modal doesn't auto-show on page load
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('cancelModal');
        if (modal) {
            // Remove any 'show' class that might be added automatically
            modal.classList.remove('show');
            modal.style.display = 'none';
            modal.setAttribute('aria-hidden', 'true');
            
            // Remove backdrop if it exists
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
        }
    });
</script>
@endsection