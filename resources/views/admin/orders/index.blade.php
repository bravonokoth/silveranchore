@extends('layouts.admin')

@section('page-title', 'Orders')

@section('content')
    @include('partials.index-table-template', [
        'title' => 'Orders',
        'createRoute' => null,
        'createLabel' => null,
        'searchRoute' => route('admin.orders.search'),
        'searchPlaceholder' => 'Search orders...',
        'items' => $orders,
        'columns' => [
            ['label' => 'Order ID', 'key' => 'id', 'type' => 'text'],
            ['label' => 'Customer', 'key' => 'user', 'type' => 'relation', 'relation' => 'user', 'relation_key' => 'name', 'fallback' => fn($item) => $item->email],
            ['label' => 'Total', 'key' => 'total', 'type' => 'currency'],
            ['label' => 'Status', 'key' => 'status', 'type' => 'text']
        ],
        'actions' => [
            ['type' => 'link', 'label' => 'View', 'route' => fn($item) => route('admin.orders.show', $item), 'class' => 'view-btn', 'icon' => 'eye'],
            ['type' => 'link', 'label' => 'Edit', 'route' => fn($item) => route('admin.orders.edit', $item), 'class' => 'edit-btn', 'icon' => 'edit'],
            ['type' => 'form', 'label' => 'Delete', 'route' => fn($item) => route('admin.orders.destroy', $item), 'method' => 'DELETE', 'class' => 'delete-btn', 'icon' => 'trash-2'],
            ['type' => 'form', 'label' => 'Cancel', 'route' => fn($item) => route('admin.orders.drop', $item), 'method' => 'POST', 'class' => 'cancel-btn', 'icon' => 'x-circle']
        ],
        'pagination' => $orders
    ])
@endsection