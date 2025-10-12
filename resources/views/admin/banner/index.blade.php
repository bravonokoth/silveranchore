@extends('layouts.admin')

@section('page-title', 'Banners')

@section('content')
    @include('partials.index-table-template', [
        'title' => 'Banners',
        'createRoute' => route('admin.banners.create'),
        'createLabel' => 'Create Banner',
        'searchRoute' => route('admin.banners.search'),
        'searchPlaceholder' => 'Search banners...',
        'items' => $banners,
        'columns' => [
            ['label' => 'Title', 'key' => 'title', 'type' => 'text'],
            ['label' => 'Image', 'key' => 'image_path', 'type' => 'image'],
            ['label' => 'Link', 'key' => 'link', 'type' => 'text'],
            ['label' => 'Active', 'key' => 'is_active', 'type' => 'boolean']
        ],
        'actions' => [
            ['type' => 'link', 'label' => 'Edit', 'route' => fn($item) => route('admin.banners.edit', $item), 'class' => 'edit-btn', 'icon' => 'edit'],
            ['type' => 'form', 'label' => 'Delete', 'route' => fn($item) => route('admin.banners.destroy', $item), 'method' => 'DELETE', 'class' => 'delete-btn', 'icon' => 'trash-2']
        ],
        'pagination' => $banners
    ])
@endsection