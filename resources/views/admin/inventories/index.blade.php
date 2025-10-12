@extends('layouts.admin')

@section('content')
    <div class="max-w-6xl mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Inventory</h2>
        <a href="{{ route('admin.inventories.create') }}" class="bg-blue-600 text-white py-2 px-4 rounded mb-4 inline-block">Add Inventory</a>
        <table class="w-full bg-white rounded shadow">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-3 text-left">Product</th>
                    <th class="p-3 text-left">Quantity</th>
                    <th class="p-3 text-left">Type</th>
                    <th class="p-3 text-left">Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($inventories as $inventory)
                    <tr>
                        <td class="p-3">{{ $inventory->product->name }}</td>
                        <td class="p-3">{{ $inventory->quantity }}</td>
                        <td class="p-3">{{ $inventory->type }}</td>
                        <td class="p-3">{{ $inventory->notes ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $inventories->links() }}
    </div>
@endsection