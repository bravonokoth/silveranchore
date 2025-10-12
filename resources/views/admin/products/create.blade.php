@extends('layouts.admin')

@section('content')
<link href="{{ asset('css/product-form.css') }}" rel="stylesheet">

<div class="product-form-page">
    <!-- Header -->
    <div class="sticky-header">
     <div class="flex justify-between items-center">
            <h2 class="text-2xl font-semibold text-gray-800 flex items-center gap-3">
             
                Create New Product
            </h2>
            <a href="{{ route('admin.products.index') }}" class="action-btn">
                
                Back to Products
            </a>
        </div>
    </div>

    <!-- Product Form -->
    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="product-form">
        @csrf

        <!-- Basic Info Section -->
        <div class="form-section">
            <h3 class="section-title">Basic Information</h3>
            <div class="form-grid">
                <!-- Category -->
                <div class="form-group">
                    <label for="category_id" class="form-label">Category</label>
                    <select name="category_id" class="form-input" required>
                        <option value="">Select Category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <!-- Product Name -->
                <div class="form-group">
                    <label for="name" class="form-label">Product Name</label>
                    <input type="text" name="name" class="form-input" placeholder="Enter product name" required>
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <!-- Slug -->
                <div class="form-group">
                    <label for="slug" class="form-label">Slug</label>
                    <input type="text" name="slug" class="form-input" placeholder="Auto-generated or custom" required>
                    @error('slug') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <!-- Description -->
                <div class="form-group col-span-full">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" rows="4" class="form-input" placeholder="Write a short description..."></textarea>
                </div>

                <!-- Price -->
                <div class="form-group">
                    <label for="price" class="form-label">Price</label>
                    <input type="number" name="price" step="0.01" class="form-input" placeholder="e.g. 49.99" required>
                    @error('price') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <!-- Discount Price -->
                <div class="form-group">
                    <label for="discount_price" class="form-label">Discount Price</label>
                    <input type="number" name="discount_price" step="0.01" class="form-input" placeholder="Optional">
                </div>

                <!-- SKU -->
                <div class="form-group">
                    <label for="sku" class="form-label">SKU</label>
                    <input type="text" name="sku" class="form-input" placeholder="e.g. PRD-001" required>
                    @error('sku') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <!-- Stock -->
                <div class="form-group">
                    <label for="stock" class="form-label">Stock Quantity</label>
                    <input type="number" name="stock" class="form-input" placeholder="e.g. 120" required>
                    @error('stock') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- SEO Settings Section -->
        <div class="form-section">
            <h3 class="section-title">
                <i data-lucide="search" class="w-5 h-5 text-blue-600 mr-2"></i> SEO Settings
            </h3>
            <div class="form-grid">
                <!-- SEO Title -->
                <div class="form-group">
                    <label for="seo_title" class="form-label">SEO Title</label>
                    <input type="text" name="seo_title" class="form-input" placeholder="Search engine title">
                </div>

                <!-- SEO Description -->
                <div class="form-group">
                    <label for="seo_description" class="form-label">SEO Description</label>
                    <input type="text" name="seo_description" class="form-input" placeholder="Meta description">
                </div>

                <!-- Product Image -->
                <div class="form-group">
                    <label for="image" class="form-label">Product Image</label>
                    <input type="file" name="image" class="form-input file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100">
                    @error('image') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Toggles -->
        <div class="form-section">
            <h3 class="section-title">Status</h3>
            <div class="form-grid">
                <div class="form-group flex items-center gap-6">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" class="form-checkbox">
                        <span class="text-sm font-medium text-gray-700">Active</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_featured" class="form-checkbox">
                        <span class="text-sm font-medium text-gray-700">Featured Product</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="form-actions">
            <button type="submit" class="action-btn">
                <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
                Create Product
            </button>
        </div>
    </form>
</div>

{{-- Lucide Icons --}}
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();
</script>
@endsection