@extends('layouts.admin')

@section('page-title', 'Orders')

@section('content')
    @include('partials.index-table-template', [
        'title' => 'Orders',
        'createRoute' => null,
        'createLabel' => null,
        'searchRoute' => route('admin.orders.search'),
        'searchPlaceholder' => 'Search orders by ID, customer, email, or product...',
        'items' => $orders,
        'columns' => [
            ['label' => 'Order ID', 'key' => 'id', 'type' => 'text'],
            [
                'label' => 'Customer',
                'key' => 'user',
                'type' => 'relation',
                'relation' => 'user',
                'relation_key' => 'name',
                'fallback' => fn($item) => $item->shippingAddress ? $item->shippingAddress->name : ($item->email ?? 'Guest'),
            ],
            [
                'label' => 'Phone',
                'key' => 'shippingAddress',
                'type' => 'relation',
                'relation' => 'shippingAddress',
                'relation_key' => 'phone_number',
                'fallback' => fn($item) => $item->shippingAddress ? ($item->shippingAddress->phone_number ?? 'N/A') : 'N/A',
            ],
            [
                'label' => 'Email',
                'key' => 'email',
                'type' => 'text',
                'fallback' => fn($item) => $item->shippingAddress ? ($item->shippingAddress->email ?? 'N/A') : ($item->email ?? 'N/A'),
            ],
            [
                'label' => 'Shipping Address',
                'key' => 'shippingAddress',
                'type' => 'custom',
                'render' => fn($item) => $item->shippingAddress
                    ? ($item->shippingAddress->line1 . ', ' . $item->shippingAddress->city . ', ' . $item->shippingAddress->country)
                    : 'N/A',
            ],
            [
                'label' => 'Products Ordered',
                'key' => 'items',
                'type' => 'custom',
                'render' => fn($item) => $item->items->map(fn($orderItem) =>
                    ($orderItem->product->name ?? 'Unknown Product') . ' (x' . $orderItem->quantity . ')'
                )->implode('<br>') ?: 'No products',
            ],
            [
                'label' => 'Total',
                'key' => 'total',
                'type' => 'currency',
                'currency' => 'KSh',
            ],
            [
                'label' => 'Status',
                'key' => 'status',
                'type' => 'text',
                'render' => fn($item) => '<span class="px-2 py-1 rounded text-sm ' . ($item->status == 'pending' ? 'bg-yellow-200 text-yellow-800' : 'bg-green-200 text-green-800') . '">' . ucfirst($item->status) . '</span>',
            ],
            [
                'label' => 'Payment Status',
                'key' => 'payment_status',
                'type' => 'text',
                'render' => fn($item) => '<span class="px-2 py-1 rounded text-sm ' . ($item->payment_status == 'pending' ? 'bg-yellow-200 text-yellow-800' : 'bg-green-200 text-green-800') . '">' . ucfirst($item->payment_status) . '</span>',
            ],
            [
                'label' => 'Payment Method',
                'key' => 'payment_method',
                'type' => 'text',
                'render' => fn($item) => ucfirst($item->payment_method ?? 'Paystack'),
            ],
        ],
        'actions' => [
            ['type' => 'link', 'label' => 'View', 'route' => fn($item) => route('admin.orders.show', $item), 'class' => 'view-btn', 'icon' => 'eye'],
            ['type' => 'link', 'label' => 'Edit', 'route' => fn($item) => route('admin.orders.edit', $item), 'class' => 'edit-btn', 'icon' => 'edit'],
            ['type' => 'form', 'label' => 'Delete', 'route' => fn($item) => route('admin.orders.destroy', $item), 'method' => 'DELETE', 'class' => 'delete-btn', 'icon' => 'trash-2'],
            ['type' => 'form', 'label' => 'Cancel', 'route' => fn($item) => route('admin.orders.drop', $item), 'method' => 'POST', 'class' => 'cancel-btn', 'icon' => 'x-circle'],
        ],
        'pagination' => $orders,
    ])
@endsection