@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">My Addresses</h2>
        @if (session('success'))
            <div class="bg-green-100 text-green-800 p-4 rounded mb-4">{{ session('success') }}</div>
        @endif
        <a href="{{ route('addresses.create') }}" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 mb-4 inline-block">Add New Address</a>
        @if ($addresses->isEmpty())
            <p class="text-gray-600">No addresses found.</p>
        @else
            <div class="bg-white rounded shadow overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="p-3 text-left">Type</th>
                            <th class="p-3 text-left">Name</th>
                            <th class="p-3 text-left">Address</th>
                            <th class="p-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($addresses as $address)
                            <tr>
                                <td class="p-3">{{ ucfirst($address->type) }}</td>
                                <td class="p-3">{{ $address->first_name }} {{ $address->last_name }}</td>
                                <td class="p-3">{{ $address->line1 }}, {{ $address->city }}, {{ $address->country }}</td>
                                <td class="p-3 flex space-x-2">
                                    <a href="{{ route('addresses.edit', $address) }}" class="text-blue-600 hover:underline">Edit</a>
                                    <form action="{{ route('addresses.destroy', $address) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this address?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection