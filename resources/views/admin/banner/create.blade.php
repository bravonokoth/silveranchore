@extends('layouts.admin')

@section('content')
    @include('partials.product-form-template', [
        'title' => 'Create Banner',
        'backRoute' => route('admin.banners.index'),
        'backLabel' => 'Back to Banners',
        'formAction' => route('admin.banners.store'),
        'sections' => [
            [
                'title' => 'Banner Information',
                'fields' => [
                    [
                        'name' => 'title',
                        'label' => 'Title',
                        'type' => 'text',
                        'required' => true,
                        'placeholder' => 'Enter banner title'
                    ],
                    [
                        'name' => 'image_path',
                        'label' => 'Image',
                        'type' => 'file',
                        'required' => true
                    ],
                    [
                        'name' => 'link',
                        'label' => 'Link',
                        'type' => 'url',
                        'placeholder' => 'Enter banner link'
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
        'submitLabel' => 'Create Banner'
    ])
@endsection