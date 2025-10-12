@extends('layouts.admin')

@section('content')
    @include('partials.product-form-template', [
        'title' => 'Edit Category',
        'backRoute' => route('admin.categories.index'),
        'backLabel' => 'Back to Categories',
        'formAction' => route('admin.categories.update', $category),
        'isEdit' => true,
        'enctype' => true,
        'values' => [
            'name' => $category->name,
            'description' => $category->description,
            'parent_id' => $category->parent_id
        ],
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
                        'placeholder' => 'Write a short description...',
                        'rows' => 4
                    ],
                    [
                        'name' => 'parent_id',
                        'label' => 'Parent Category',
                        'type' => 'select',
                        'options' => array_merge([['value' => '', 'label' => 'None']], $categories->map(fn($cat) => ['value' => $cat->id, 'label' => $cat->name])->toArray())
                    ],
                    [
                        'name' => 'image',
                        'label' => 'Image',
                        'type' => 'file'
                    ]
                ]
            ]
        ],
        'submitLabel' => 'Update Category'
    ])
@endsection