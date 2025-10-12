@extends('layouts.admin')

@section('content')
    @include('partials.product-form-template', [
        'title' => 'Add Inventory',
        'backRoute' => route('admin.inventories.index'),
        'backLabel' => 'Back to Inventories',
        'formAction' => route('admin.inventories.store'),
        'sections' => [
            [
                'title' => 'Inventory Information',
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
                        'name' => 'type',
                        'label' => 'Type',
                        'type' => 'select',
                        'required' => true,
                        'options' => [
                            ['value' => 'adjustment', 'label' => 'Adjustment'],
                            ['value' => 'restock', 'label' => 'Restock'],
                            ['value' => 'sale', 'label' => 'Sale']
                        ]
                    ],
                    [
                        'name' => 'notes',
                        'label' => 'Notes',
                        'type' => 'textarea',
                        'fullWidth' => true,
                        'placeholder' => 'Enter any notes'
                    ]
                ]
            ]
        ],
        'submitLabel' => 'Add Inventory'
    ])
@endsection