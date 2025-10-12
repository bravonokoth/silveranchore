@extends('layouts.admin')

@section('content')
    @include('partials.product-form-template', [
        'title' => 'Edit Banner',
        'backRoute' => route('admin.banner.index'),
        'backLabel' => 'Back to Banners',
        'formAction' => route('admin.banner.update', $banner),
        'isEdit' => true,
        'enctype' => true,
        'values' => [
            'title' => $banner->title,
            'link' => $banner->link,
            'is_active' => $banner->is_active
        ],
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
                        'type' => 'file'
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
        'submitLabel' => 'Update Banner'
    ])
@endsection