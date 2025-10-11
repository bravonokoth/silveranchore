@extends('layouts.admin')

@section('content')
    <div class="max-w-6xl mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Orders</h2>
        <table class="w-full bg-white rounded shadow">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-3 text-left">Order ID</th>
                    <th class="p-3 text-left">Customer</th>
                    <th class="p-3 text-left">Total</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td class="p-3">{{ $order->id }}</td>
                        <td class="p-3">{{ $order->user ? $order->user->name : $order->email }}</td>
                        <td class="p-3">${{ $order->total }}</td>
                        <td class="p-3">{{ $order->status }}</td>
                        <td class="p-3">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600">View</a>
                            <a href="{{ route('admin.orders.edit', $order) }}" class="text-green-600 ml-2">Edit</a>
                            <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 ml-2">Delete</button>
                            </form>
                            <form action="{{ route('admin.orders.drop', $order) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-red-600 ml-2">Cancel</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $orders->links() }}
    </div>
@endsection