@extends('layouts.admin')

@section('content')
    @include('partials.product-form-template', [
        'title' => 'Upload Media',
        'backRoute' => route('admin.media.index'),
        'backLabel' => 'Back to Media',
        'formAction' => route('admin.media.store'),
        'sections' => [
            [
                'title' => 'Media Information',
                'fields' => [
                    [
                        'name' => 'model_type',
                        'label' => 'Model Type',
                        'type' => 'select',
                        'required' => true,
                        'options' => [
                            ['value' => 'App\Models\Product', 'label' => 'Product'],
                            ['value' => 'App\Models\Category', 'label' => 'Category']
                        ]
                    ],
                    [
                        'name' => 'model_id',
                        'label' => 'Model ID',
                        'type' => 'number',
                        'required' => true,
                        'placeholder' => 'Enter model ID'
                    ],
                    [
                        'name' => 'path',
                        'label' => 'File',
                        'type' => 'file',
                        'required' => true
                    ],
                    [
                        'name' => 'type',
                        'label' => 'Type',
                        'type' => 'select',
                        'required' => true,
                        'options' => [
                            ['value' => 'image', 'label' => 'Image'],
                            ['value' => 'video', 'label' => 'Video']
                        ]
                    ]
                ]
            ]
        ],
        'submitLabel' => 'Upload Media'
    ])
@endsection