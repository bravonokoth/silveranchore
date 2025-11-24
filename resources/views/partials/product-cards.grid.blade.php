{{-- resources/views/partials/product-cards-grid.blade.php --}}
@foreach($products as $product)
    @include('partials.product-card', compact('product'))
@endforeach