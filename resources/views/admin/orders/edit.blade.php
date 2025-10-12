@extends('layouts.admin')

@section('content')
    @include('partials.product-form-template', [
        'title' => 'Edit Order #' . $order->id,
        'backRoute' => route('admin.orders.index'),
        'backLabel' => 'Back to Orders',
        'formAction' => route('admin.orders.update', $order),
        'isEdit' => true,
        'enctype' => false,
        'values' => [
            'status' => $order->status,
            'shipping_address' => $order->shipping_address
        ],
        'sections' => [
            [
                'title' => 'Order Information',
                'fields' => [
                    [
                        'name' => 'status',
                        'label' => 'Status',
                        'type' => 'select',
                        'required' => true,
                        'options' => [
                            ['value' => 'pending', 'label' => 'Pending'],
                            ['value' => 'processing', 'label' => 'Processing'],
                            ['value' => 'shipped', 'label' => 'Shipped'],
                            ['value' => 'delivered', 'label' => 'Delivered'],
                            ['value' => 'canceled', 'label' => 'Canceled']
                        ]
                    ],
                    [
                        'name' => 'shipping_address',
                        'label' => 'Shipping Address',
                        'type' => 'text',
                        'required' => true,
                        'placeholder' => 'Enter shipping address'
                    ]
                ]
            ]
        ],
        'submitLabel' => 'Update Order'
    ])
@endsection