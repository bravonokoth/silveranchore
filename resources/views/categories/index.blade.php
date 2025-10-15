@extends('layouts.app')

@section('content')
<!-- Display Success Message -->
@if (session('success'))
    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    </div>
@endif

<!-- Category Section (Slick Carousel WITH IMAGES) -->
<section class="max-w-7xl mx-auto py-8">
    <h2 class="text-2xl font-semibold mb-6 text-gray-800">Our Categories</h2>
    <div class="js-slick-carousel u-slick u-slick--gutters-3 u-slick--equal-height"
         data-slides-show="4"
         data-slides-scroll="3"
         data-infinite="true"
         data-pagi-classes="text-center u-slick__pagination mt-7 mb-0"
         data-responsive='[{
           "breakpoint": 992,
           "settings": { "slidesToShow": 3 }
         }, {
           "breakpoint": 720,
           "settings": { "slidesToShow": 2 }
         }, {
           "breakpoint": 480,
           "settings": { "slidesToShow": 1 }
         }]'>
        
        
        @foreach ($categories as $category)
            <div class="js-slide">
                <div class="category-card text-center">
                    <div class="category-image mb-3">
                        <img src="{{ asset('storage/' . ($category->media->first()?->path ?? 'images/category-placeholder.jpg')) }}" 
                             alt="{{ $category->name }}" 
                             class="w-full h-32 object-cover rounded-lg">
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">{{ $category->name }}</h3>
                    <p class="text-sm text-gray-600 mb-2">{{ $category->products_count ?? $category->products()->count() }} Products</p>
                    <a href="{{ route('categories.show', $category->id) }}" class="text-gold hover:underline">View Products</a>
                </div>
            </div>
        @endforeach
    </div>
    <div class="text-center u-slick__pagination mt-7 mb-0"></div>
</section>

<x-features-section />
@endsection