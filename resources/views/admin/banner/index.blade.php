@extends('layouts.admin')

@section('content')
    <div class="max-w-6xl mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Banners</h2>
        <a href="{{ route('admin.banners.create') }}" class="bg-blue-600 text-white py-2 px-4 rounded mb-4 inline-block">Create Banner</a>
        <table class="w-full bg-white rounded shadow">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-3 text-left">Title</th>
                    <th class="p-3 text-left">Image</th>
                    <th class="p-3 text-left">Link</th>
                    <th class="p-3 text-left">Active</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($banners as $banner)
                    <tr>
                        <td class="p-3">{{ $banner->title }}</td>
                        <td class="p-3">
                            <img src="{{ asset('storage/' . $banner->image_path) }}" alt="{{ $banner->title }}" class="h-16">
                        </td>
                        <td class="p-3">{{ $banner->link ?? 'N/A' }}</td>
                        <td class="p-3">{{ $banner->is_active ? 'Yes' : 'No' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $banners->links() }}
    </div>
@endsection