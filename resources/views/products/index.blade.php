{{-- resources/views/products/index.blade.php --}}
@extends('layouts.app')

@section('content')
<!-- Success Message -->
@if (session('success'))
    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    </div>
@endif

<section class="products max-w-7xl mx-auto py-8 px-4">
    
    <div class="products-filter-bar">
        <div class="filter-header-row">
            <h2 class="text-2xl font-semibold mb-0 text-gray-800 dark:text-gray-200">Our Products</h2>
            <button id="toggleProductFilters" class="toggle-filters-mobile">
                <i class="fas fa-sliders-h"></i> Filters
            </button>
        </div>

        <div id="productFiltersPanel" class="filters-panel">
            <!-- Top Row Filters -->
            <div class="filter-row">
                <div class="filter-item">
                    <label><i class="fas fa-search"></i> Search</label>
                    <input type="text" id="productSearch" placeholder="Search products..." class="filter-input">
                </div>
                <div class="filter-item">
                    <label><i class="fas fa-tags"></i> Category</label>
                    <select id="productCategoryFilter" class="filter-select">
                        <option value="">All Categories</option>
                        @foreach($categories ?? [] as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-item">
                    <label><i class="fas fa-box"></i> Stock</label>
                    <select id="stockFilter" class="filter-select">
                        <option value="">All</option>
                        <option value="in-stock">In Stock</option>
                        <option value="out-stock">Out of Stock</option>
                    </select>
                </div>
                <div class="filter-item">
                    <label><i class="fas fa-sort"></i> Sort By</label>
                    <select id="productSort" class="filter-select">
                        <option value="">Default</option>
                        <option value="name-asc">Name (A-Z)</option>
                        <option value="name-desc">Name (Z-A)</option>
                        <option value="price-asc">Price (Low-High)</option>
                        <option value="price-desc">Price (High-Low)</option>
                    </select>
                </div>
                <div class="filter-item">
                    <label>&nbsp;</label>
                    <button id="resetProductFilters" class="reset-btn-products">
                        <i class="fas fa-redo"></i> Reset
                    </button>
                </div>
            </div>

            <!-- Price Range -->
            <div class="price-range-section">
                <label><i class="fas fa-dollar-sign"></i> Price Range</label>
                <div class="price-inputs">
                    <input type="number" id="minPrice" placeholder="Min Price" class="filter-input price-input">
                    <span class="price-separator">to</span>
                    <input type="number" id="maxPrice" placeholder="Max Price" class="filter-input price-input">
                    <button id="applyPriceFilter" class="apply-price-btn">
                        <i class="fas fa-check"></i> Apply
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Info -->
    <div class="products-results-info">
        Showing <span id="productResultCount">{{ $products->count() }}</span> of 
        <span id="totalProductCount">{{ $products->total() }}</span> products
    </div>

    <!-- NEW GRID USING YOUR PRODUCT CARD -->
    <div id="products-grid-container" 
         class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 auto-rows-fr">
         
        @foreach($products as $product)
            <div class="product-grid-item"
                 data-name="{{ strtolower($product->name) }}"
                 data-category="{{ $product->category_id }}"
                 data-category-name="{{ strtolower($product->category?->name ?? 'uncategorized') }}"
                 data-price="{{ $product->discount_price && $product->discount_price < $product->price ? $product->discount_price : $product->price }}"
                 data-stock="{{ $product->stock }}">
                 
                @include('partials.product-card', compact('product'))
            </div>
        @endforeach
    </div>

    <!-- No Results -->
    <div id="noProductResults" class="no-results-message text-center py-20" style="display: none;">
        <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
        <p class="text-lg text-gray-600 mb-6">No products found matching your criteria</p>
        <button id="clearProductFilters" class="clear-filters-btn">
            <i class="fas fa-times"></i> Clear Filters
        </button>
    </div>

    <!-- Pagination -->
    <div class="mt-12">
        {{ $products->links() }}
    </div>
</section>
@endsection

@push('styles')
<style>
    /* Reuse the exact grid styles from your product-card.blade.php */
    #products-grid-container {
        display: grid;
        grid-template-columns: 1fr;
        gap: 24px;
        padding: 0 16px;
    }
    @media (min-width: 640px) { #products-grid-container { grid-template-columns: repeat(2, 1fr); } }
    @media (min-width: 1024px) { #products-grid-container { grid-template-columns: repeat(3, 1fr); } }
    @media (min-width: 1280px) { 
        #products-grid-container { 
            grid-template-columns: repeat(4, 1fr);
            padding: 0;
            max-width: 1400px;
            margin: 0 auto;
        }
    }
    @media (max-width: 639px) {
        #products-grid-container { padding: 0 20px; }
        .product-card-grid { max-width: 380px; margin: 0 auto; }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const allItems = document.querySelectorAll('.product-grid-item');
    const totalCount = allItems.length;

    document.getElementById('totalProductCount').textContent = totalCount;

    function filterAndSort() {
        const search = document.getElementById('productSearch').value.toLowerCase().trim();
        const category = document.getElementById('productCategoryFilter').value;
        const minPrice = parseFloat(document.getElementById('minPrice').value) || 0;
        const maxPrice = parseFloat(document.getElementById('maxPrice').value) || Infinity;
        const stockFilter = document.getElementById('stockFilter').value;
        const sortBy = document.getElementById('productSort').value;

        let visible = [];
        let visibleCount = 0;

        allItems.forEach(item => {
            const name = (item.dataset.name || '').toLowerCase();
            const itemCategory = item.dataset.category;
            const price = parseFloat(item.dataset.price) || 0;
            const stock = parseInt(item.dataset.stock) || 0;

            let show = true;

            if (search && !name.includes(search)) show = false;
            if (category && itemCategory !== category) show = false;
            if (price < minPrice || price > maxPrice) show = false;
            if (stockFilter === 'in-stock' && stock === 0) show = false;
            if (stockFilter === 'out-stock' && stock > 0) show = false;

            item.style.display = show ? '' : 'none';
            if (show) {
                visible.push(item);
                visibleCount++;
            }
        });

        // Sorting
        if (sortBy && visible.length > 1) {
            visible.sort((a, b) => {
                switch(sortBy) {
                    case 'name-asc':  return (a.dataset.name || '').localeCompare(b.dataset.name || '');
                    case 'name-desc': return (b.dataset.name || '').localeCompare(a.dataset.name || '');
                    case 'price-asc': return (parseFloat(a.dataset.price) || 0) - (parseFloat(b.dataset.price) || 0);
                    case 'price-desc':return (parseFloat(b.dataset.price) || 0) - (parseFloat(a.dataset.price) || 0);
                    default: return 0;
                }
            });
            const container = document.getElementById('products-grid-container');
            visible.forEach(el => container.appendChild(el));
        }

        document.getElementById('productResultCount').textContent = visibleCount;
        document.getElementById('noProductResults').style.display = visibleCount === 0 ? 'block' : 'none';
    }

    // Event Listeners
    document.getElementById('productSearch').addEventListener('keyup', filterAndSort);
    document.get->{'ById'}('productCategoryFilter').addEventListener('change', filterAndSort);
    document.getElementById('stockFilter').addEventListener('change', filterAndSort);
    document.getElementById('productSort').addEventListener('change', filterAndSort);
    document.getElementById('applyPriceFilter').addEventListener('click', filterAndSort);
    document.getElementById('minPrice').addEventListener('keypress', e => e.key === 'Enter' && filterAndSort());
    document.getElementById('maxPrice').addEventListener('keypress', e => e.key === 'Enter' && filterAndSort());

    // Reset & Clear
    document.getElementById('resetProductFilters').addEventListener('click', () => {
        document.getElementById('productSearch').value = '';
        document.getElementById('productCategoryFilter').value = '';
        document.getElementById('stockFilter').value = '';
        document.getElementById('productSort').value = '';
        document.getElementById('minPrice').value = '';
        document.getElementById('maxPrice').value = '';
        filterAndSort();
    });
    document.getElementById('clearProductFilters').addEventListener('click', () => {
        document.querySelectorAll('#productFiltersPanel input, #productFiltersPanel select').forEach(el => el.value = '');
        filterAndSort();
    });

    // Mobile filter toggle
    document.getElementById('toggleProductFilters').addEventListener('click', function() {
        const panel = document.getElementById('productFiltersPanel');
        panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
        this.querySelector('i').classList.toggle('fa-times');
    });
});
</script>
@endpush

<style>
/* Products Filter Bar */
.products-filter-bar {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.filter-header-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.toggle-filters-mobile {
    display: none;
    padding: 10px 18px;
    background: var(--ice-blue);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    gap: 8px;
    align-items: center;
    transition: all 0.3s ease;
}

.toggle-filters-mobile:hover {
    background: var(--ice-accent);
}

.filters-panel {
    display: block;
}

.filter-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    align-items: end;
    margin-bottom: 15px;
}

.filter-item {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.filter-item label {
    font-size: 13px;
    font-weight: 600;
    color: #475569;
    display: flex;
    align-items: center;
    gap: 6px;
}

.filter-item label i {
    color: var(--ice-blue);
    font-size: 12px;
}

.filter-input,
.filter-select {
    padding: 10px 12px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
    background: white;
}

.filter-input:focus,
.filter-select:focus {
    outline: none;
    border-color: var(--ice-blue);
    box-shadow: 0 0 0 3px var(--ice-glow);
}

/* Price Range Section - Full Width Below */
.price-range-section {
    padding-top: 15px;
    border-top: 2px solid #f1f5f9;
    margin-top: 5px;
}

.price-range-section > label {
    font-size: 13px;
    font-weight: 600;
    color: #475569;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 10px;
}

.price-range-section > label i {
    color: var(--ice-blue);
    font-size: 12px;
}

.price-inputs {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.price-input {
    flex: 1;
    min-width: 120px;
    padding: 10px 12px;
}

.price-separator {
    color: #94a3b8;
    font-weight: 600;
    font-size: 14px;
}

.apply-price-btn {
    padding: 10px 20px;
    background: var(--ice-blue);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
}

.apply-price-btn:hover {
    background: var(--ice-accent);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px var(--ice-glow);
}

.reset-btn-products {
    width: 100%;
    padding: 10px 12px;
    background: white;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    color: #64748b;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.reset-btn-products:hover {
    background: #f8fafc;
    border-color: var(--ice-blue);
    color: var(--ice-blue);
}

.products-results-info {
    font-size: 14px;
    color: #64748b;
    margin-bottom: 15px;
    padding: 0 5px;
}

.products-results-info span {
    font-weight: 600;
    color: var(--ice-blue);
}

.no-results-message {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 12px;
    margin: 20px 0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.no-results-message i {
    font-size: 48px;
    color: #cbd5e1;
    margin-bottom: 15px;
}

.no-results-message p {
    font-size: 16px;
    color: #64748b;
    margin-bottom: 20px;
}

.clear-filters-btn {
    padding: 10px 24px;
    background: var(--ice-blue);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.clear-filters-btn:hover {
    background: var(--ice-accent);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px var(--ice-glow);
}

/* Dark Mode */
body.dark .products-filter-bar,
body.dark .no-results-message {
    background: #2d3748;
}

body.dark .filter-input,
body.dark .filter-select,
body.dark .reset-btn-products {
    background: #1a202c;
    border-color: #4a5568;
    color: #e2e8f0;
}

body.dark .filter-item label,
body.dark .price-range-section > label {
    color: #cbd5e1;
}

body.dark .products-results-info {
    color: #94a3b8;
}

body.dark .price-range-section {
    border-top-color: #4a5568;
}

/* Responsive */
@media (max-width: 768px) {
    .toggle-filters-mobile {
        display: flex;
    }
    
    .filters-panel {
        display: none;
        margin-top: 15px;
    }
    
    .filter-row {
        grid-template-columns: 1fr;
        gap: 12px;
    }
    
    .price-inputs {
        flex-direction: column;
        align-items: stretch;
    }
    
    .price-input {
        width: 100%;
    }
    
    .price-separator {
        display: none;
    }
    
    .apply-price-btn {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .toggle-filters-mobile {
        font-size: 13px;
        padding: 8px 14px;
    }

    .price-inputs {
        gap: 8px;
    }

    .apply-price-btn {
        padding: 10px 16px;
        font-size: 13px;
    }
}
</style>

<style>
    /* MAIN GRID CONTAINERS */
    #products-grid-container {
        display: grid;
        gap: 20px;
        padding: 0 12px;
    }

    /* Mobile: 2 cards per row (your new request) */
    @media (max-width: 767px) {
        #products-grid-container {
            grid-template-columns: 1fr 1fr;        /* ← 2 cards on phone */
            padding: 0 16px;
        }
        .product-card-grid {
            max-width: none;
            margin: 0;
            height: 420px;
        }
    }

    /* Tablet: 3 cards */
    @media (min-width: 768px) and (max-width: 1023px) {
        #products-grid-container {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    /* Desktop: 4 cards + centered container */
    @media (min-width: 1024px) {
        #products-grid-container {
            grid-template-columns: repeat(4, 1fr);
            max-width: 1400px;
            margin: 0 auto;
            padding: 0;
        }
    }

    /* PUSH PRICE & STOCK TO THE FAR RIGHT ON BIG SCREENS */
    @media (min-width: 768px) {
        .product-card-grid .card-body {
            grid-template-columns: 1fr auto !important;   /* ← forces price to far right */
            gap: 1rem;
        }

        .product-card-grid .product-right {
            justify-items: end;
            text-align: right;
        }

        .product-card-grid .price-info,
        .product-card-grid .stock-info {
            justify-content: flex-end;
        }
    }

    /* Optional: make mobile cards a bit smaller so 2 fit nicely */
    @media (max-width: 480px) {
        #products-grid-container {
            gap: 16px;
            padding: 0 12px;
        }
        .product-card-grid {
            height: 400px;
        }
        .product-card-grid .card-img-top {
            height: 220px;
        }
    }
</style>