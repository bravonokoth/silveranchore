@extends('layouts.admin')

@section('content')
    <div class="max-w-6xl mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Products</h2>
        <a href="{{ route('admin.products.create') }}" class="bg-blue-600 text-white py-2 px-4 rounded mb-4 inline-block">Create Product</a>
        <table class="w-full bg-white rounded shadow">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-3 text-left">Name</th>
                    <th class="p-3 text-left">Category</th>
                    <th class="p-3 text-left">Price</th>
                    <th class="p-3 text-left">Stock</th>
                    <th class="p-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td class="p-3">{{ $product->name }}</td>
                        <td class="p-3">{{ $product->category->name }}</td>
                        <td class="p-3">${{ $product->price }}</td>
                        <td class="p-3">{{ $product->stock }}</td>
                        <td class="p-3">
                            <a href="{{ route('admin.products.show', $product) }}" class="text-blue-600">View</a>
                            <a href="{{ route('admin.products.edit', $product) }}" class="text-green-600 ml-2">Edit</a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 ml-2">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $products->links() }}
    </div>
@endsection