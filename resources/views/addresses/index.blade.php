@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800">🏠 My Addresses</h2>
        <a href="{{ route('addresses.create') }}" 
           class="bg-blue-600 text-white py-2 px-4 rounded-xl hover:bg-blue-700 transition duration-200 shadow">
            + Add New Address
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl mb-6 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if ($addresses->isEmpty())
        <div class="text-center py-16">
            <p class="text-gray-600 text-lg mb-4">No addresses found yet.</p>
            <a href="{{ route('addresses.create') }}" 
               class="inline-block px-5 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition duration-200 shadow">
                Add Your First Address
            </a>
        </div>
    @else
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($addresses as $address)
                <div class="bg-white border border-gray-100 rounded-2xl shadow hover:shadow-lg transition duration-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-sm uppercase font-semibold text-gray-500 tracking-wide">
                            {{ ucfirst($address->type) }}
                        </span>
                        <span class="inline-block px-3 py-1 text-xs rounded-full 
                            @if($address->type === 'home') bg-blue-100 text-blue-700 
                            @elseif($address->type === 'work') bg-green-100 text-green-700 
                            @else bg-gray-100 text-gray-700 @endif">
                            {{ ucfirst($address->type) }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <h3 class="text-lg font-semibold text-gray-800">{{ $address->first_name }} {{ $address->last_name }}</h3>
                    </div>

                    <p class="text-gray-600 text-sm mb-4">
                        {{ $address->line1 }}<br>
                        {{ $address->city }}, {{ $address->country }}
                    </p>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('addresses.edit', $address) }}" 
                           class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                            ✏️ Edit
                        </a>
                        <form action="{{ route('addresses.destroy', $address) }}" method="POST" 
                              onsubmit="return confirm('Are you sure you want to delete this address?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="text-red-600 hover:text-red-800 font-medium text-sm">
                                🗑️ Delete
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
