@extends('layouts.admin')

@section('page-title', 'Coupons')

@section('content')
    @include('partials.index-table-template', [
        'title' => 'Coupons',
        'createRoute' => route('admin.coupons.create'),
        'createLabel' => 'Create Coupon',
        'searchRoute' => route('admin.coupons.search'),
        'searchPlaceholder' => 'Search coupons...',
        'items' => $coupons,
        'columns' => [
            ['label' => 'Code', 'key' => 'code', 'type' => 'text'],
            ['label' => 'Discount (%)', 'key' => 'discount', 'type' => 'text'],
            ['label' => 'Expires At', 'key' => 'expires_at', 'type' => 'date'],
            ['label' => 'Active', 'key' => 'is_active', 'type' => 'boolean']
        ],
        'actions' => [
            ['type' => 'link', 'label' => 'Edit', 'route' => fn($item) => route('admin.coupons.edit', $item), 'class' => 'edit-btn', 'icon' => 'edit'],
            ['type' => 'form', 'label' => 'Delete', 'route' => fn($item) => route('admin.coupons.destroy', $item), 'method' => 'DELETE', 'class' => 'delete-btn', 'icon' => 'trash-2']
        ],
        'pagination' => $coupons
    ])
@endsection