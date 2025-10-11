@extends('layouts.admin')

@section('content')
    <div class="max-w-lg mx-auto p-6 bg-white rounded shadow">
        <h2 class="text-2xl font-bold mb-4">Edit Category</h2>
        <form method="POST" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" name="name" value="{{ $category->name }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ $category->description }}</textarea>
            </div>
            <div class="mb-4">
                <label for="parent_id" class="block text-sm font-medium text-gray-700">Parent Category</label>
                <select name="parent_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <option value="">None</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $category->parent_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label for="image" class="block text-sm font-medium text-gray-700">Image</label>
                <input type="file" name="image" class="mt-1 block w-full">
                @error('image') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Update</button>
        </form>
    </div>
@endsection