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
         <!-- Left Column - Image -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    @php
                        $mediaImage = $category->media->first();
                    @endphp
                    
                    @if ($mediaImage)
                        <div class="aspect-square">
                            <img src="{{ asset('storage/' . $mediaImage->path) }}" 
                                 alt="{{ $category->name }}" 
                                 class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="aspect-square bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                            <div class="text-center">
                                <svg class="w-24 h-24 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-gray-500 font-medium">No image uploaded</p>
                            </div>
                        </div>
                    @endif
                    
                    <div class="p-4 border-t border-gray-100">
                        <h3 class="font-semibold text-gray-800 text-lg mb-1">{{ $category->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $category->slug }}</p>
                    </div>
                </div>
            </div>


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