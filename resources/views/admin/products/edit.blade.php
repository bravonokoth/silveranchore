@extends('layouts.admin')

@section('content')
    <div class="max-w-lg mx-auto p-6 bg-white rounded shadow">
        <h2 class="text-2xl font-bold mb-4">Edit Product</h2>
        <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
                <select name="category_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" name="name" value="{{ $product->name }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="mb-4">
                <label for="slug" class="block text-sm font-medium text-gray-700">Slug</label>
                <input type="text" name="slug" value="{{ $product->slug }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                @error('slug') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ $product->description }}</textarea>
            </div>
            <div class="mb-4">
                <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
                <input type="number" name="price" step="0.01" value="{{ $product->price }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                @error('price') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="mb-4">
                <label for="discount_price" class="block text-sm font-medium text-gray-700">Discount Price</label>
                <input type="number" name="discount_price" step="0.01" value="{{ $product->discount_price }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="mb-4">
                <label for="stock" class="block text-sm font-medium text-gray-700">Stock</label>
                <input type="number" name="stock" value="{{ $product->stock }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                @error('stock') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="mb-4">
                <label for="sku" class="block text-sm font-medium text-gray-700">SKU</label>
                <input type="text" name="sku" value="{{ $product->sku }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                @error('sku') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="mb-4">
                <label for="seo_title" class="block text-sm font-medium text-gray-700">SEO Title</label>
                <input type="text" name="seo_title" value="{{ $product->seo_title }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="mb-4">
                <label for="seo_description" class="block text-sm font-medium text-gray-700">SEO Description</label>
                <input type="text" name="seo_description" value="{{ $product->seo_description }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" {{ $product->is_active ? 'checked' : '' }} class="mr-2">
                    <span class="text-sm font-medium text-gray-700">Active</span>
                </label>
            </div>
            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="is_featured" {{ $product->is_featured ? 'checked' : '' }} class="mr-2">
                    <span class="text-sm font-medium text-gray-700">Featured</span>
                </label>
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