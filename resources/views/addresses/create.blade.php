@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Add New Address</h2>
        @if (session('success'))
            <div class="bg-green-100 text-green-800 p-4 rounded mb-4">{{ session('success') }}</div>
        @endif
        <form method="POST" action="{{ route('addresses.store') }}" class="space-y-4">
            @csrf
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700">Address Type</label>
                <select name="type" id="type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="shipping">Shipping</option>
                    <option value="billing">Billing</option>
                </select>
                @error('type')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                <input type="text" name="first_name" id="first_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="{{ old('first_name') }}" required>
                @error('first_name')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                <input type="text" name="last_name" id="last_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="{{ old('last_name') }}" required>
                @error('last_name')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="{{ old('email', auth()->user()->email) }}" required>
                @error('email')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone</label>
                <input type="text" name="phone_number" id="phone_number" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="{{ old('phone_number') }}" required>
                @error('phone_number')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="line1" class="block text-sm font-medium text-gray-700">Address Line 1</label>
                <input type="text" name="line1" id="line1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="{{ old('line1') }}" required>
                @error('line1')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="line2" class="block text-sm font-medium text-gray-700">Address Line 2 (Optional)</label>
                <input type="text" name="line2" id="line2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="{{ old('line2') }}">
                @error('line2')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                <input type="text" name="city" id="city" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="{{ old('city', 'Nairobi') }}" required>
                @error('city')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="state" class="block text-sm font-medium text-gray-700">State (Optional)</label>
                <input type="text" name="state" id="state" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="{{ old('state', 'Nairobi') }}">
                @error('state')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="postal_code" class="block text-sm font-medium text-gray-700">Postal Code (Optional)</label>
                <input type="text" name="postal_code" id="postal_code" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="{{ old('postal_code') }}">
                @error('postal_code')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="country" class="block text-sm font-medium text-gray-700">Country</label>
                <input type="text" name="country" id="country" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="{{ old('country', 'Kenya') }}" required>
                @error('country')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="flex space-x-4">
                <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">Save Address</button>
                <a href="{{ route('addresses.index') }}" class="bg-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-400">Cancel</a>
            </div>
        </form>
    </div>
@endsection