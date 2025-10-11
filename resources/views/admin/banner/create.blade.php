@extends('layouts.admin')

@section('content')
    <div class="max-w-lg mx-auto p-6 bg-white rounded shadow">
        <h2 class="text-2xl font-bold mb-4">Create Banner</h2>
        <form method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                <input type="text" name="title" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                @error('title') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="mb-4">
                <label for="image_path" class="block text-sm font-medium text-gray-700">Image</label>
                <input type="file" name="image_path" class="mt-1 block w-full" required>
                @error('image_path') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="mb-4">
                <label for="link" class="block text-sm font-medium text-gray-700">Link</label>
                <input type="url" name="link" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                @error('link') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" class="mr-2">
                    <span class="text-sm font-medium text-gray-700">Active</span>
                </label>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Create</button>
        </form>
    </div>
@endsection