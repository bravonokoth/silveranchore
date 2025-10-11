@extends('layouts.admin')

@section('content')
    <div class="max-w-lg mx-auto p-6 bg-white rounded shadow">
        <h2 class="text-2xl font-bold mb-4">Create Coupon</h2>
        <form method="POST" action="{{ route('admin.coupons.store') }}">
            @csrf
            <div class="mb-4">
                <label for="code" class="block text-sm font-medium text-gray-700">Code</label>
                <input type="text" name="code" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                @error('code') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="mb-4">
                <label for="discount" class="block text-sm font-medium text-gray-700">Discount (%)</label>
                <input type="number" name="discount" min="0" max="100" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                @error('discount') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="mb-4">
                <label for="expires_at" class="block text-sm font-medium text-gray-700">Expires At</label>
                <input type="date" name="expires_at" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" class="mr-2">
                    <span class="text-sm font-medium text-gray-700">Active</span>
                </label>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Create</button>
        </form>
    </div>
@endsection