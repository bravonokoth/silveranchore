@extends('layouts.admin')

@section('content')
    <div class="max-w-6xl mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Purchases</h2>
        <a href="{{ route('admin.purchases.create') }}" class="bg-blue-600 text-white py-2 px-4 rounded mb-4 inline-block">Record Purchase</a>
        <table class="w-full bg-white rounded shadow">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-3 text-left">Product</th>
                    <th class="p-3 text-left">Quantity</th>
                    <th class="p-3 text-left">Cost</th>
                    <th class="p-3 text-left">Supplier</th>
                    <th class="p-3 text-left">Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($purchases as $purchase)
                    <tr>
                        <td class="p-3">{{ $purchase->product->name }}</td>
                        <td class="p-3">{{ $purchase->quantity }}</td>
                        <td class="p-3">${{ $purchase->cost }}</td>
                        <td class="p-3">{{ $purchase->supplier ?? 'N/A' }}</td>
                        <td class="p-3">{{ $purchase->purchase_date }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $purchases->links() }}
    </div>
@endsection