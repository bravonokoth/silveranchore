@extends('layouts.admin')

@section('content')
    <div class="max-w-6xl mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Media</h2>
        <a href="{{ route('admin.media.create') }}" class="bg-blue-600 text-white py-2 px-4 rounded mb-4 inline-block">Upload Media</a>
        <table class="w-full bg-white rounded shadow">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-3 text-left">Model</th>
                    <th class="p-3 text-left">Type</th>
                    <th class="p-3 text-left">Path</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($media as $item)
                    <tr>
                        <td class="p-3">{{ $item->model_type }} (ID: {{ $item->model_id }})</td>
                        <td class="p-3">{{ $item->type }}</td>
                        <td class="p-3">
                            <img src="{{ asset('storage/' . $item->path) }}" alt="Media" class="h-16">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $media->links() }}
    </div>
@endsection