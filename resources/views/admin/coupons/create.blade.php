@extends('layouts.admin')

@section('content')
    @include('partials.product-form-template', [
        'title' => 'Create Coupon',
        'backRoute' => route('admin.coupons.index'),
        'backLabel' => 'Back to Coupons',
        'formAction' => route('admin.coupons.store'),
        'sections' => [
            [
                'title' => 'Coupon Information',
                'fields' => [
                    [
                        'name' => 'code',
                        'label' => 'Code',
                        'type' => 'text',
                        'required' => true,
                        'placeholder' => 'Enter coupon code'
                    ],
                    [
                        'name' => 'discount',
                        'label' => 'Discount (%)',
                        'type' => 'number',
                        'required' => true,
                        'step' => '1',
                        'placeholder' => 'e.g. 10'
                    ],
                    [
                        'name' => 'expires_at',
                        'label' => 'Expires At',
                        'type' => 'date'
                    ],
                    [
                        'type' => 'checkbox-group',
                        'checkboxes' => [
                            [
                                'name' => 'is_active',
                                'label' => 'Active'
                            ]
                        ]
                    ]
                ]
            ]
        ],
        'submitLabel' => 'Create Coupon'
    ])
@endsection