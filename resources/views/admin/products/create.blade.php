@extends('layouts.admin')

@section('content')
    @include('partials.product-form-template', [
        'title' => 'Create New Product',
        'backRoute' => route('admin.products.index'),
        'backLabel' => 'Back to Products',
        'formAction' => route('admin.products.store'),
        'sections' => [
            [
                'title' => 'Basic Information',
                'fields' => [
                    [
                        'name' => 'category_id',
                        'label' => 'Category',
                        'type' => 'select',
                        'required' => true,
                        'options' => array_merge([['value' => '', 'label' => 'Select Category']], $categories->map(fn($category) => ['value' => $category->id, 'label' => $category->name])->toArray())
                    ],
                    [
                        'name' => 'name',
                        'label' => 'Product Name',
                        'type' => 'text',
                        'required' => true,
                        'placeholder' => 'Enter product name'
                    ],
                    [
                        'name' => 'slug',
                        'label' => 'Slug',
                        'type' => 'text',
                        'required' => true,
                        'placeholder' => 'Auto-generated or custom'
                    ],
                    [
                        'name' => 'description',
                        'label' => 'Description',
                        'type' => 'textarea',
                        'fullWidth' => true,
                        'placeholder' => 'Write a short description...',
                        'rows' => 4
                    ],
                    [
                        'name' => 'price',
                        'label' => 'Price',
                        'type' => 'number',
                        'required' => true,
                        'step' => '0.01',
                        'placeholder' => 'e.g. 49.99'
                    ],
                    [
                        'name' => 'discount_price',
                        'label' => 'Discount Price',
                        'type' => 'number',
                        'step' => '0.01',
                        'placeholder' => 'Optional'
                    ],
                    [
                        'name' => 'sku',
                        'label' => 'SKU',
                        'type' => 'text',
                        'required' => true,
                        'placeholder' => 'e.g. PRD-001'
                    ],
                    [
                        'name' => 'stock',
                        'label' => 'Stock Quantity',
                        'type' => 'number',
                        'required' => true,
                        'placeholder' => 'e.g. 120'
                    ]
                ]
            ],
            [
                'title' => 'SEO Settings',
                'icon' => 'search',
                'fields' => [
                    [
                        'name' => 'seo_title',
                        'label' => 'SEO Title',
                        'type' => 'text',
                        'placeholder' => 'Search engine title'
                    ],
                    [
                        'name' => 'seo_description',
                        'label' => 'SEO Description',
                        'type' => 'text',
                        'placeholder' => 'Meta description'
                    ],
                    [
                        'name' => 'image',
                        'label' => 'Product Image',
                        'type' => 'file'
                    ]
                ]
            ],
        [
    'title' => 'Status',
    'fields' => [
        [
            'type' => 'checkbox-group',
            'checkboxes' => [
                [
                    'name' => 'is_active',
                    'label' => 'Active'
                ],
                [
                    'name' => 'is_featured',
                    'label' => 'Featured Product'
                ]
            ]
        ]
    ]
]
        ],
        'submitLabel' => 'Create Product'
    ])
@endsection