@extends('layouts.admin')

@section('content')
    <div class="max-w-lg mx-auto p-6 bg-white rounded shadow">
        <h2 class="text-2xl font-bold mb-4">Record Purchase</h2>
        <form method="POST" action="{{ route('admin.purchases.store') }}">
            @csrf
            <div class="mb-4">
                <label for="product_id" class="block text-sm font-medium text-gray-700">Product</label>
                <select name="product_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
                @error('product_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="mb-4">
                <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
                <input type="number" name="quantity" min="1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                @error('quantity') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="mb-4">
                <label for="cost" class="block text-sm font-medium text-gray-700">Cost</label>
                <input type="number" name="cost" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                @error('cost') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="mb-4">
                <label for="supplier" class="block text-sm font-medium text-gray-700">Supplier</label>
                <input type="text" name="supplier" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="mb-4">
                <label for="purchase_date" class="block text-sm font-medium text-gray-700">Purchase Date</label>
                <input type="date" name="purchase_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                @error('purchase_date') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Record</button>
        </form>
    </div>
@endsection