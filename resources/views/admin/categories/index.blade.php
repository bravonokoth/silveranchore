@extends('layouts.admin')

@section('content')
    <div class="max-w-6xl mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Categories</h2>
        <a href="{{ route('admin.categories.create') }}" class="bg-blue-600 text-white py-2 px-4 rounded mb-4 inline-block">Create Category</a>
        <form action="{{ route('admin.categories.search') }}" method="GET" class="mb-4">
            <input type="text" name="search" placeholder="Search categories..." class="border-gray-300 rounded-md p-2">
            <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded">Search</button>
        </form>
        <table class="w-full bg-white rounded shadow">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-3 text-left">Name</th>
                    <th class="p-3 text-left">Description</th>
                    <th class="p-3 text-left">Products</th>
                    <th class="p-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                    <tr>
                        <td class="p-3">{{ $category->name }}</td>
                        <td class="p-3">{{ $category->description ?? 'N/A' }}</td>
                        <td class="p-3">{{ $category->products_count }}</td>
                        <td class="p-3">
                            <a href="{{ route('admin.categories.show', $category) }}" class="text-blue-600">View</a>
                            <a href="{{ route('admin.categories.edit', $category) }}" class="text-green-600 ml-2">Edit</a>
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 ml-2">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $categories->links() ?? '' }}
    </div>
@endsection