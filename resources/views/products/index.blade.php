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

<section class="products max-w-7xl mx-auto py-8">
    <h2 class="text-2xl font-semibold mb-6 text-gray-800 dark:text-gray-200">Our Products</h2>
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
        
        @foreach ($products as $product)
            <div class="js-slide">
                <!-- Product -->
                <div class="card text-center w-100">
                    <div class="position-relative">
                        <img class="card-img-top" 
                             src="{{ asset('storage/' . ($product->media->first()?->path ?? 'images/placeholder.jpg')) }}" 
                             alt="{{ $product->name }}">
                        
                        @if ($product->stock == 0)
                            <div class="position-absolute top-0 left-0 pt-3 pl-3">
                                <span class="badge badge-danger badge-pill">Sold out</span>
                            </div>
                        @endif

                        <div class="position-absolute top-0 right-0 pt-3 pr-3">
                            <button type="button" class="btn btn-sm btn-icon btn-outline-secondary rounded-circle" data-toggle="tooltip" data-placement="top" title="Save for later">
                                <span class="fas fa-heart btn-icon__inner"></span>
                            </button>
                        </div>
                    </div>

                    <div class="card-body pt-4 px-4 pb-0">
                        <div class="mb-2">
                            <a class="d-inline-block text-secondary small font-weight-medium mb-1" 
                               href="{{ route('categories.show', $product->category_id) }}">
                                {{ $product->category?->name ?? 'Uncategorized' }}
                            </a>
                            <h3 class="font-size-1 font-weight-normal">
                                <a class="text-secondary" href="{{ route('products.show', $product->id) }}">
                                    {{ $product->name }}
                                </a>
                            </h3>
                            <div class="d-block font-size-1">
                                <span class="font-weight-medium">Ksh {{ number_format($product->price, 2) }}</span>
                                @if ($product->discount_price && $product->discount_price < $product->price)
                                    <span class="text-secondary ml-1">
                                        <del>Ksh {{ number_format($product->price, 2) }}</del>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card-footer border-0 pt-0 pb-4 px-4">
                        {{-- ✅ REAL RATINGS (default 0) --}}
                        <div class="mb-3">
                            <a class="d-inline-flex align-items-center small" href="#">
                                <div class="text-warning mr-2">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <small class="{{ $i <= ($product->rating ?? 0) ? 'fas fa-star' : 'far fa-star text-muted' }}"></small>
                                    @endfor
                                </div>
                                <span class="text-secondary">{{ $product->review_count ?? 0 }}</span>
                            </a>
                        </div>
                        
                        {{-- ✅ REAL CART BUTTON --}}
                        <form action="{{ route('cart.store') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" 
                                    class="btn btn-sm btn-outline-primary btn-sm-wide btn-pill transition-3d-hover {{ $product->stock == 0 ? 'disabled' : '' }}">
                                <i class="fas fa-shopping-cart me-1"></i>Add to Cart
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="text-center u-slick__pagination mt-7 mb-0"></div>
    
    {{-- ✅ PAGINATION --}}
    <div class="mt-8">
        {{ $products->links() }}
    </div>
</section>
@endsection