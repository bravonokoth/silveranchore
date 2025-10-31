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
                <!-- Product Card -->
                <div class="card text-center w-100">
                    <div class="position-relative">
                        <img class="card-img-top" 
                             src="{{ asset('storage/' . ($product->media->first()?->path ?? 'images/placeholder.jpg')) }}" 
                             alt="{{ $product->name }}"
                             style="height: 250px; object-fit: cover;">
                        
                        <!-- Sold Out Badge Only -->
                        @if ($product->stock == 0)
                            <div class="position-absolute top-0 left-0 pt-3 pl-3">
                                <span class="badge badge-danger badge-pill">Sold Out</span>
                            </div>
                        @endif

                        <!-- Wishlist Heart -->
                        <div class="position-absolute top-0 right-0 pt-3 pr-3">
                            <button type="button" 
                                    class="btn btn-sm btn-icon btn-outline-secondary rounded-circle" 
                                    data-toggle="tooltip" data-placement="top" title="Save for later">
                                <span class="fas fa-heart btn-icon__inner"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Title & Category -->
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
                        </div>
                    </div>

                    <!-- Footer: Buttons, Buy Now with Price, Rating -->
                    <div class="card-footer border-0 pt-0 pb-4 px-4">
                        <!-- 1. Add to Cart + View Details -->
                        <div class="btn-group d-flex gap-2 justify-content-center mb-3" role="group">
                            <form action="{{ route('cart.store') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" value="1">
                                @if($product->stock > 0)
                                    <button type="submit" 
                                            class="btn btn-sm btn-outline-primary btn-sm-wide">
                                        <i class="fas fa-shopping-cart me-1"></i>Add to Cart
                                    </button>
                                @else
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-secondary btn-sm-wide" disabled>
                                        <i class="fas fa-ban me-1"></i>Sold Out
                                    </button>
                                @endif
                            </form>

                            <a href="{{ route('products.show', $product->id) }}" 
                               class="btn btn-sm btn-outline-info btn-sm-wide">
                                <i class="fas fa-eye me-1"></i>View Details
                            </a>
                        </div>

                        <!-- 2. Buy Now Button WITH Price -->
                        @if($product->stock > 0)
                            <a href="{{ route('checkout.quick', $product->id) }}" 
                               class="btn btn-success btn-sm-wide d-block mx-auto text-center mb-2">
                                <i class="fas fa-credit-card me-1"></i>
                                Buy Now - 
                                @if ($product->discount_price && $product->discount_price < $product->price)
                                    <span class="text-white">Ksh {{ number_format($product->discount_price, 2) }}</span>
                                    <del class="text-white-50 ms-1">Ksh {{ number_format($product->price, 2) }}</del>
                                @else
                                    <span class="text-white">Ksh {{ number_format($product->price, 2) }}</span>
                                @endif
                            </a>
                        @endif

                        <!-- 3. Rating -->
                        <div class="text-center">
                            <div class="d-inline-flex align-items-center small">
                                <div class="text-warning me-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <small class="{{ $i <= ($product->rating ?? 0) ? 'fas fa-star' : 'far fa-star text-muted' }}"></small>
                                    @endfor
                                </div>
                                <span class="text-secondary">({{ $product->review_count ?? 0 }})</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="text-center u-slick__pagination mt-7 mb-0"></div>
    <div class="mt-8">
        {{ $products->links() }}
    </div>
</section>
@endsection