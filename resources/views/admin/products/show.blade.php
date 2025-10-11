@extends('layouts.admin')

@section('content')
    <div class="max-w-lg mx-auto p-6 bg-white rounded shadow">
        <h2 class="text-2xl font-bold mb-4">{{ $product->name }}</h2>
        <p><strong>Category:</strong> {{ $product->category->name }}</p>
        <p><strong>Description:</strong> {{ $product->description ?? 'N/A' }}</p>
        <p><strong>Price:</strong> ${{ $product->price }}</p>
        <p><strong>Discount Price:</strong> ${{ $product->discount_price ?? 'N/A' }}</p>
        <p><strong>Stock:</strong> {{ $product->stock }}</p>
        <p><strong>SKU:</strong> {{ $product->sku }}</p>
        <p><strong>SEO Title:</strong> {{ $product->seo_title ?? 'N/A' }}</p>
        <p><strong>SEO Description:</strong> {{ $product->seo_description ?? 'N/A' }}</p>
        <p><strong>Active:</strong> {{ $product->is_active ? 'Yes' : 'No' }}</p>
        <p><strong>Featured:</strong> {{ $product->is_featured ? 'Yes' : 'No' }}</p>
        @if ($product->media->first())
            <img src="{{ asset('storage/' . $product->media->first()->path) }}" alt="{{ $product->name }}" class="h-32 mt-4">
        @endif
        <a href="{{ route('admin.products.edit', $product) }}" class="bg-blue-600 text-white py-2 px-4 rounded mt-4 inline-block">Edit</a>
    </div>
@endsection