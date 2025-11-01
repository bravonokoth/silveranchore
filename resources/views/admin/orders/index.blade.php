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
                'type' => 'custom',
                'render' => fn($item) => $item->user 
                    ? $item->user->name 
                    : ($item->shippingAddress->name ?? 'Guest Customer'),
            ],
            [
                'label' => 'Phone',
                'key' => 'phone',
                'type' => 'custom',
                'render' => fn($item) => $item->shippingAddress->phone_number ?? 'N/A',
            ],
            [
                'label' => 'Email',
                'key' => 'email',
                'type' => 'custom',
                'render' => fn($item) => $item->email ?? ($item->shippingAddress->email ?? 'N/A'),
            ],
            [
                'label' => 'Shipping Address',
                'key' => 'shippingAddress',
                'type' => 'custom',
                'render' => fn($item) => $item->shippingAddress
                    ? '<div class="text-sm">' . 
                        htmlspecialchars($item->shippingAddress->line1) . '<br>' . 
                        htmlspecialchars($item->shippingAddress->city) . ', ' . 
                        htmlspecialchars($item->shippingAddress->country) . 
                      '</div>'
                    : '<span class="text-gray-500">N/A</span>',
            ],
            [
                'label' => 'Products',
                'key' => 'items',
                'type' => 'custom',
                'render' => fn($item) => $item->items->isNotEmpty()
                    ? '<div class="text-sm space-y-1">' . 
                        $item->items->map(fn($orderItem) =>
                            '<div class="flex items-center gap-2">' .
                                '<span class="font-medium">' . htmlspecialchars($orderItem->product->name ?? 'Unknown') . '</span>' .
                                '<span class="text-gray-500">×' . $orderItem->quantity . '</span>' .
                            '</div>'
                        )->implode('') . 
                      '</div>'
                    : '<span class="text-gray-500">No products</span>',
            ],
            [
                'label' => 'Total',
                'key' => 'total',
                'type' => 'custom',
                'render' => fn($item) => '<span class="font-semibold text-green-600">KSh ' . number_format($item->total, 2) . '</span>',
            ],
            [
                'label' => 'Status',
                'key' => 'status',
                'type' => 'custom',
                'render' => fn($item) => match($item->status) {
                    'pending' => '<span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>',
                    'processing' => '<span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Processing</span>',
                    'completed' => '<span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Completed</span>',
                    'cancelled' => '<span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Cancelled</span>',
                    default => '<span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">' . ucfirst($item->status) . '</span>',
                },
            ],
            [
                'label' => 'Payment',
                'key' => 'payment_status',
                'type' => 'custom',
                'render' => fn($item) => match($item->payment_status) {
                    'pending' => '<span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>',
                    'paid' => '<span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Paid</span>',
                    'failed' => '<span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Failed</span>',
                    'refunded' => '<span class="px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Refunded</span>',
                    default => '<span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">' . ucfirst($item->payment_status) . '</span>',
                },
            ],
            [
                'label' => 'Payment Method',
                'key' => 'payment_method',
                'type' => 'custom',
                'render' => fn($item) => '<span class="text-sm capitalize">' . 
                    ($item->payment_method ? htmlspecialchars($item->payment_method) : 'Paystack') . 
                    '</span>',
            ],
            [
                'label' => 'Date',
                'key' => 'created_at',
                'type' => 'custom',
                'render' => fn($item) => '<span class="text-sm text-gray-600">' . 
                    $item->created_at->format('M d, Y') . '<br>' . 
                    '<span class="text-xs text-gray-500">' . $item->created_at->format('h:i A') . '</span>' .
                    '</span>',
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