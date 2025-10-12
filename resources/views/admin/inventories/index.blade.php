@extends('layouts.admin')

@section('page-title', 'Inventory')

@section('content')
    @include('partials.index-table-template', [
        'title' => 'Inventory',
        'createRoute' => route('admin.inventories.create'),
        'createLabel' => 'Add Inventory',
        'searchRoute' => route('admin.inventories.search'),
        'searchPlaceholder' => 'Search inventory...',
        'items' => $inventories,
        'columns' => [
            ['label' => 'Product', 'key' => 'product', 'type' => 'relation', 'relation' => 'product', 'relation_key' => 'name'],
            ['label' => 'Quantity', 'key' => 'quantity', 'type' => 'text'],
            ['label' => 'Type', 'key' => 'type', 'type' => 'text'],
            ['label' => 'Notes', 'key' => 'notes', 'type' => 'text']
        ],
        'actions' => [
            ['type' => 'link', 'label' => 'Edit', 'route' => fn($item) => route('admin.inventories.edit', $item), 'class' => 'edit-btn', 'icon' => 'edit'],
            ['type' => 'form', 'label' => 'Delete', 'route' => fn($item) => route('admin.inventories.destroy', $item), 'method' => 'DELETE', 'class' => 'delete-btn', 'icon' => 'trash-2']
        ],
        'pagination' => $inventories
    ])
@endsection