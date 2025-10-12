@extends('layouts.admin')

@section('page-title', 'Purchases')

@section('content')
    @include('partials.index-table-template', [
        'title' => 'Purchases',
        'createRoute' => route('admin.purchases.create'),
        'createLabel' => 'Record Purchase',
        'searchRoute' => route('admin.purchases.search'),
        'searchPlaceholder' => 'Search purchases...',
        'items' => $purchases,
        'columns' => [
            ['label' => 'Product', 'key' => 'product', 'type' => 'relation', 'relation' => 'product', 'relation_key' => 'name'],
            ['label' => 'Quantity', 'key' => 'quantity', 'type' => 'text'],
            ['label' => 'Cost', 'key' => 'cost', 'type' => 'currency'],
            ['label' => 'Supplier', 'key' => 'supplier', 'type' => 'text'],
            ['label' => 'Date', 'key' => 'purchase_date', 'type' => 'date']
        ],
        'actions' => [
            ['type' => 'link', 'label' => 'Edit', 'route' => fn($item) => route('admin.purchases.edit', $item), 'class' => 'edit-btn', 'icon' => 'edit'],
            ['type' => 'form', 'label' => 'Delete', 'route' => fn($item) => route('admin.purchases.destroy', $item), 'method' => 'DELETE', 'class' => 'delete-btn', 'icon' => 'trash-2']
        ],
        'pagination' => $purchases
    ])
@endsection