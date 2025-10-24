@extends('layouts.admin')

@section('page-title', 'Category Details')

@section('content')
    <div class="max-w-4xl mx-auto p-6">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-start mb-4">
                <h2 class="text-3xl font-bold text-gray-800">{{ $category->name }}</h2>
                <div class="flex gap-2">
                    <a href="{{ route('admin.categories.edit', $category) }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded inline-flex items-center transition">
                        <i data-feather="edit" class="w-4 h-4 mr-2"></i>
                        Edit
                    </a>
                    <a href="{{ route('admin.categories.index') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded inline-flex items-center transition">
                        <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i>
                        Back
                    </a>
                </div>
            </div>

            <!-- Category Image -->
            @if ($category->image)
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Category Image</label>
                    <div class="border rounded-lg overflow-hidden inline-block">
                        <img src="{{ asset('storage/' . $category->image) }}" 
                             alt="{{ $category->name }}" 
                             class="max-h-64 w-auto object-cover"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <div style="display:none;" class="p-4 bg-red-50 text-red-600">
                            <i data-feather="alert-circle" class="w-4 h-4 inline"></i>
                            Image not found: {{ $category->image }}
                        </div>
                    </div>
                </div>
            @else
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Category Image</label>
                    <div class="bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                        <i data-feather="image" class="w-12 h-12 mx-auto text-gray-400 mb-2"></i>
                        <p class="text-gray-500">No image uploaded</p>
                    </div>
                </div>
            @endif

            <!-- Category Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                    <p class="text-gray-600 bg-gray-50 p-3 rounded border">
                        {{ $category->description ?? 'No description provided' }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Parent Category</label>
                    <p class="text-gray-600 bg-gray-50 p-3 rounded border">
                        {{ $category->parent ? $category->parent->name : 'None (Top Level)' }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Slug</label>
                    <p class="text-gray-600 bg-gray-50 p-3 rounded border font-mono text-sm">
                        {{ $category->slug }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Products Count</label>
                    <p class="text-gray-600 bg-gray-50 p-3 rounded border">
                        <span class="font-bold text-lg text-blue-600">{{ $category->products->count() }}</span> products
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Created At</label>
                    <p class="text-gray-600 bg-gray-50 p-3 rounded border">
                        {{ $category->created_at->format('M d, Y h:i A') }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Updated At</label>
                    <p class="text-gray-600 bg-gray-50 p-3 rounded border">
                        {{ $category->updated_at->format('M d, Y h:i A') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Products in Category -->
        @if ($category->products->count() > 0)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Products in this Category</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($category->products as $product)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $product->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">${{ number_format($product->price, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $product->stock ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('admin.products.show', $product) }}" 
                                           class="text-blue-600 hover:text-blue-900">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <script>
        // Initialize Feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    </script>
@endsection