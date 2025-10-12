@extends('layouts.admin')

@section('page-title', 'Media')

@section('content')
    @include('partials.index-table-template', [
        'title' => 'Media',
        'createRoute' => route('admin.media.create'),
        'createLabel' => 'Upload Media',
        'searchRoute' => route('admin.media.search'),
        'searchPlaceholder' => 'Search media...',
        'items' => $media,
        'columns' => [
            ['label' => 'Model', 'key' => 'model_type', 'type' => 'text'],
            ['label' => 'Model ID', 'key' => 'model_id', 'type' => 'text'],
            ['label' => 'Type', 'key' => 'type', 'type' => 'text'],
            ['label' => 'Path', 'key' => 'path', 'type' => 'image']
        ],
        'actions' => [
            ['type' => 'link', 'label' => 'Edit', 'route' => fn($item) => route('admin.media.edit', $item), 'class' => 'edit-btn', 'icon' => 'edit'],
            ['type' => 'form', 'label' => 'Delete', 'route' => fn($item) => route('admin.media.destroy', $item), 'method' => 'DELETE', 'class' => 'delete-btn', 'icon' => 'trash-2']
        ],
        'pagination' => $media
    ])
@endsection