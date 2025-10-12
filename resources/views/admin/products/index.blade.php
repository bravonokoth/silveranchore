@extends('layouts.admin')

@section('page-title', 'All Products')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-800">All Products</h1>

    <a href="{{ route('admin.products.create') }}"
       class="bg-blue-900 hover:bg-blue-800 text-white font-medium py-2 px-5 rounded-2xl shadow-md transition-all duration-300 ease-in-out transform hover:scale-105">
        + Create Product
    </a>
</div>

<div class="bg-white rounded-xl shadow-md p-6">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-100 border-b">
                <th class="py-3 px-4 text-sm font-semibold text-gray-600 uppercase">#</th>
                <th class="py-3 px-4 text-sm font-semibold text-gray-600 uppercase">Product Code</th>
                <th class="py-3 px-4 text-sm font-semibold text-gray-600 uppercase">Name</th>
                <th class="py-3 px-4 text-sm font-semibold text-gray-600 uppercase">Category</th>
                <th class="py-3 px-4 text-sm font-semibold text-gray-600 uppercase">Stock</th>
                <th class="py-3 px-4 text-sm font-semibold text-gray-600 uppercase">Unit Price</th>
                <th class="py-3 px-4 text-sm font-semibold text-gray-600 uppercase">Sales Price</th>
                <th class="py-3 px-4 text-sm font-semibold text-gray-600 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($products as $key => $product)
                <tr class="hover:bg-gray-50 transition-all">
                    <td class="py-3 px-4">{{ $key + 1 }}</td>
                    <td class="py-3 px-4">{{ $product->product_code }}</td>
                    <td class="py-3 px-4">{{ $product->name }}</td>
                    <td class="py-3 px-4">{{ $product->category->name ?? '—' }}</td>
                    <td class="py-3 px-4">{{ $product->stock }}</td>
                    <td class="py-3 px-4">{{ $product->unit_price }}</td>
                    <td class="py-3 px-4">{{ $product->sales_unit_price }}</td>
                    <td class="py-3 px-4">
                        <div class="flex gap-3">
                            <a href="{{ url('purchase-products/'.$product->id) }}"
                               class="text-green-600 hover:text-green-800 font-semibold transition">
                                Purchase
                            </a>
                            <a href="{{ route('admin.categories.edit', $product->category_id) }}"
                               class="text-blue-600 hover:text-blue-800 font-semibold transition">
                                Edit
                            </a>
                            <form action="{{ route('admin.categories.destroy', $product->category_id) }}" method="POST"
                                  onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 font-semibold transition">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
