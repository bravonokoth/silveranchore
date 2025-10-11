@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">About Us</h2>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 mb-4">
                SilverAnchorE is your trusted online store for premium products in Kenya. We pride ourselves on offering high-quality items, exceptional customer service, and fast, reliable delivery. Our mission is to make shopping convenient and enjoyable for everyone.
            </p>
            <p class="text-gray-600 mb-4">
                Founded in 2025, we aim to bring the best products to your doorstep, whether you're in Nairobi or beyond. Our team is dedicated to ensuring your shopping experience is seamless and satisfying.
            </p>
            <a href="{{ route('contact') }}" class="text-blue-600 hover:underline">Contact us for any inquiries!</a>
        </div>
    </div>
@endsection