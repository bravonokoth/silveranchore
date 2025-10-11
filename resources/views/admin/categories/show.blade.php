@extends('layouts.admin')

@section('content')
    <div class="max-w-lg mx-auto p-6 bg-white rounded shadow">
        <h2 class="text-2xl font-bold mb-4">{{ $category->name }}</h2>
        <p><strong>Description:</strong> {{ $category->description ?? 'N/A' }}</p>
        <p><strong>Parent:</strong> {{ $category->parent ? $category->parent->name : 'None' }}</p>
        <p><strong>Products:</strong> {{ $category->products->count() }}</p>
        @if ($category->image)
            <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="h-32 mt-4">
        @endif
        <a href="{{ route('admin.categories.edit', $category) }}" class="bg-blue-600 text-white py-2 px-4 rounded mt-4 inline-block">Edit</a>
    </div>
@endsection