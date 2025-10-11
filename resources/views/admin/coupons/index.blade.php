@extends('layouts.admin')

@section('content')
    <div class="max-w-6xl mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Coupons</h2>
        <a href="{{ route('admin.coupons.create') }}" class="bg-blue-600 text-white py-2 px-4 rounded mb-4 inline-block">Create Coupon</a>
        <table class="w-full bg-white rounded shadow">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-3 text-left">Code</th>
                    <th class="p-3 text-left">Discount (%)</th>
                    <th class="p-3 text-left">Expires At</th>
                    <th class="p-3 text-left">Active</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($coupons as $coupon)
                    <tr>
                        <td class="p-3">{{ $coupon->code }}</td>
                        <td class="p-3">{{ $coupon->discount }}</td>
                        <td class="p-3">{{ $coupon->expires_at ?? 'N/A' }}</td>
                        <td class="p-3">{{ $coupon->is_active ? 'Yes' : 'No' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $coupons->links() }}
    </div>
@endsection