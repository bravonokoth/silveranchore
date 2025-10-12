@extends('layouts.admin')

@section('content')
    @include('partials.product-form-template', [
        'title' => 'Create Category',
        'backRoute' => route('admin.categories.index'),
        'backLabel' => 'Back to Categories',
        'formAction' => route('admin.categories.store'),
        'sections' => [
            [
                'title' => 'Category Information',
                'fields' => [
                    [
                        'name' => 'name',
                        'label' => 'Name',
                        'type' => 'text',
                        'required' => true,
                        'placeholder' => 'Enter category name'
                    ],
                    [
                        'name' => 'description',
                        'label' => 'Description',
                        'type' => 'textarea',
                        'fullWidth' => true,
                        'placeholder' => 'Write a short description...'
                    ],
                    [
                        'name' => 'parent_id',
                        'label' => 'Parent Category',
                        'type' => 'select',
                        'options' => array_merge([['value' => '', 'label' => 'None']], $categories->map(fn($category) => ['value' => $category->id, 'label' => $category->name])->toArray())
                    ],
                    [
                        'name' => 'image',
                        'label' => 'Image',
                        'type' => 'file'
                    ]
                ]
            ]
        ],
        'submitLabel' => 'Create Category'
    ])
@endsection