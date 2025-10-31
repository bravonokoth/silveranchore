@extends('layouts.admin')

@section('content')

    {{-- Show validation errors --}}
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <strong>Validation failed:</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @include('partials.product-form-template', [
        'title'       => 'Edit Order #' . $order->id,
        'backRoute'   => route('admin.orders.index'),
        'backLabel'   => 'Back to Orders',
        'formAction'  => route('admin.orders.update', $order),
        'isEdit'      => true,
        'enctype'     => false,

        {{-- Pre-filled values (must match the field names) --}}
        'values' => [
            'status'          => $order->status,
            'payment_status'  => $order->payment_status,
            'shipping_address'=> $order->shipping_address,
        ],

        'sections' => [
            [
                'title'  => 'Order Information',
                'fields' => [

                    // ---------- STATUS ----------
                    [
                        'name'     => 'status',
                        'label'    => 'Status',
                        'type'     => 'select',
                        'required' => true,
                        'options'  => [
                            ['value' => 'pending',    'label' => 'Pending'],
                            ['value' => 'processing', 'label' => 'Processing'],
                            ['value' => 'shipped',    'label' => 'Shipped'],
                            ['value' => 'delivered',  'label' => 'Delivered'],
                            ['value' => 'canceled',   'label' => 'Canceled'],
                        ],
                    ],

                    // ---------- PAYMENT STATUS ----------
                    [
                        'name'     => 'payment_status',
                        'label'    => 'Payment Status',
                        'type'     => 'select',
                        'required' => true,
                        'options'  => [
                            ['value' => 'pending',  'label' => 'Pending'],
                            ['value' => 'paid',     'label' => 'Paid'],
                            ['value' => 'failed',   'label' => 'Failed'],
                            ['value' => 'refunded', 'label' => 'Refunded'],
                        ],
                    ],

                    // ---------- SHIPPING ADDRESS ----------
                    [
                        'name'       => 'shipping_address',
                        'label'      => 'Shipping Address',
                        'type'       => 'text',
                        'required'   => true,
                        'placeholder'=> 'Enter shipping address',
                    ],
                ],
            ],
        ],

        'submitLabel' => 'Update Order',
    ])
@endsection