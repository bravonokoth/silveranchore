@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">{{ $category->name }}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse ($products as $product)
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <img src="{{ $product->media->where('type', 'image')->first() ? asset('storage/' . $product->media->where('type', 'image')->first()->path) : 'https://via.placeholder.com/300' }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <h3 class="text-lg font-semibold">{{ $product->name }}</h3>
                        <p class="text-gray-600">KSh {{ number_format($product->price, 2) }}</p>
                        <div class="mt-2 flex space-x-2">
                            <a href="{{ route('products.show', $product) }}" class="text-blue-600 hover:underline">View</a>
                            <form action="{{ route('cart.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="text-blue-600 hover:underline">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-gray-600">No products found in this category.</p>
            @endforelse
        </div>
        <div class="mt-6">
            {{ $products->links() }}
        </div>
    </div>
@endsection