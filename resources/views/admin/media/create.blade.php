@extends('layouts.admin')

@section('content')
    <div class="max-w-lg mx-auto p-6 bg-white rounded shadow">
        <h2 class="text-2xl font-bold mb-4">Upload Media</h2>
        <form method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label for="model_type" class="block text-sm font-medium text-gray-700">Model Type</label>
                <select name="model_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    <option value="App\Models\Product">Product</option>
                    <option value="App\Models\Category">Category</option>
                </select>
                @error('model_type') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="mb-4">
                <label for="model_id" class="block text-sm font-medium text-gray-700">Model ID</label>
                <input type="number" name="model_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                @error('model_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="mb-4">
                <label for="path" class="block text-sm font-medium text-gray-700">File</label>
                <input type="file" name="path" class="mt-1 block w-full" required>
                @error('path') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="mb-4">
                <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                <select name="type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    <option value="image">Image</option>
                    <option value="video">Video</option>
                </select>
                @error('type') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Upload</button>
        </form>
    </div>
@endsection