@extends('layouts.admin')

@section('page-title', 'Categories')

@section('content')
    @include('partials.index-table-template', [
        'title' => 'Categories',
        'createRoute' => route('admin.categories.create'),
        'createLabel' => 'Create Category',
        'searchRoute' => route('admin.categories.search'),
        'searchPlaceholder' => 'Search categories...',
        'items' => $categories,
        'columns' => [
            ['label' => 'Name', 'key' => 'name', 'type' => 'text'],
            ['label' => 'Description', 'key' => 'description', 'type' => 'text'],
            ['label' => 'Products', 'key' => 'products_count', 'type' => 'text']
        ],
        'actions' => [
            ['type' => 'link', 'label' => 'View', 'route' => fn($item) => route('admin.categories.show', $item), 'class' => 'view-btn', 'icon' => 'eye'],
            ['type' => 'link', 'label' => 'Edit', 'route' => fn($item) => route('admin.categories.edit', $item), 'class' => 'edit-btn', 'icon' => 'edit'],
            ['type' => 'form', 'label' => 'Delete', 'route' => fn($item) => route('admin.categories.destroy', $item), 'method' => 'DELETE', 'class' => 'delete-btn', 'icon' => 'trash-2']
        ],
        'pagination' => $categories
    ])
@endsection