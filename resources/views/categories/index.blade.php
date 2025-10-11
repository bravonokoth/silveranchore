@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Categories</h2>
        @if ($categories->isEmpty())
            <p class="text-gray-600">No categories available.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                @foreach ($categories as $category)
                    <a href="{{ route('categories.show', $category) }}" class="bg-white rounded-lg shadow p-4 text-center hover:bg-gray-100">
                        <h3 class="text-lg font-semibold">{{ $category->name }}</h3>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endsection