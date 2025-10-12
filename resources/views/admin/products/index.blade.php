@extends('layouts.admin')

@section('page-title', 'All Products')

@section('content')
    @include('partials.index-table-template', [
        'title' => 'All Products',
        'createRoute' => route('admin.products.create'),
        'createLabel' => 'Create Product',
        'searchRoute' => route('admin.products.search'),
        'searchPlaceholder' => 'Search products...',
        'items' => $products,
        'columns' => [
            ['label' => 'Product Code', 'key' => 'product_code', 'type' => 'text'],
            ['label' => 'Name', 'key' => 'name', 'type' => 'text'],
            ['label' => 'Category', 'key' => 'category', 'type' => 'relation', 'relation' => 'category', 'relation_key' => 'name'],
            ['label' => 'Stock', 'key' => 'stock', 'type' => 'text'],
            ['label' => 'Unit Price', 'key' => 'unit_price', 'type' => 'currency'],
            ['label' => 'Sales Price', 'key' => 'sales_unit_price', 'type' => 'currency']
        ],
        'actions' => [
            ['type' => 'link', 'label' => 'Purchase', 'route' => fn($item) => url('purchase-products/' . $item->id), 'class' => 'view-btn', 'icon' => 'shopping-cart'],
            ['type' => 'link', 'label' => 'Edit', 'route' => fn($item) => route('admin.products.edit', $item), 'class' => 'edit-btn', 'icon' => 'edit'],
            ['type' => 'form', 'label' => 'Delete', 'route' => fn($item) => route('admin.products.destroy', $item), 'method' => 'DELETE', 'class' => 'delete-btn', 'icon' => 'trash-2']
        ],
        'pagination' => $products
    ])
@endsection