@extends('layouts.admin')

@section('content')
    @include('partials.product-form-template', [
        'title' => 'Edit Coupon',
        'backRoute' => route('admin.coupons.index'),
        'backLabel' => 'Back to Coupons',
        'formAction' => route('admin.coupons.update', $coupon),
        'isEdit' => true,
        'enctype' => false,
        'values' => [
            'code' => $coupon->code,
            'discount' => $coupon->discount,
            'expires_at' => $coupon->expires_at ? $coupon->expires_at->format('Y-m-d') : '',
            'is_active' => $coupon->is_active
        ],
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
        'submitLabel' => 'Update Coupon'
    ])
@endsection