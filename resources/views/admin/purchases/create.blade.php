@extends('layouts.admin')

@section('content')
    @include('partials.product-form-template', [
        'title' => 'Record Purchase',
        'backRoute' => route('admin.purchases.index'),
        'backLabel' => 'Back to Purchases',
        'formAction' => route('admin.purchases.store'),
        'sections' => [
            [
                'title' => 'Purchase Information',
                'fields' => [
                    [
                        'name' => 'product_id',
                        'label' => 'Product',
                        'type' => 'select',
                        'required' => true,
                        'options' => $products->map(fn($product) => ['value' => $product->id, 'label' => $product->name])->toArray()
                    ],
                    [
                        'name' => 'quantity',
                        'label' => 'Quantity',
                        'type' => 'number',
                        'required' => true,
                        'placeholder' => 'Enter quantity'
                    ],
                    [
                        'name' => 'cost',
                        'label' => 'Cost',
                        'type' => 'number',
                        'required' => true,
                        'step' => '0.01',
                        'placeholder' => 'e.g. 49.99'
                    ],
                    [
                        'name' => 'supplier',
                        'label' => 'Supplier',
                        'type' => 'text',
                        'placeholder' => 'Enter supplier name'
                    ],
                    [
                        'name' => 'purchase_date',
                        'label' => 'Purchase Date',
                        'type' => 'date',
                        'required' => true
                    ]
                ]
            ]
        ],
        'submitLabel' => 'Record Purchase'
    ])
@endsection