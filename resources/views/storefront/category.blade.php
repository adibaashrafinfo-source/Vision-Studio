@extends('layouts.storefront')

@section('title', $category?->name ?? 'All Products')

@push('styles')<link rel="stylesheet" href="{{ asset('storefront/category.css') }}">@endpush

@section('content')
<div class="wrap">
    <div class="crumb">
        <a href="{{ route('home') }}">Home</a><span class="sep">/</span>
        <a href="{{ route('products.index') }}">Shop</a>
        @if ($category)<span class="sep">/</span><span style="color:var(--text)">{{ $category->name }}</span>@endif
    </div>

    <div class="cat-hero">
        <div>
            <div class="eyebrow">{{ $category ? 'Browse the collection' : 'Explore' }}</div>
            <h1>{{ $category?->name ?? (request('q') ? 'Search: '.request('q') : 'All Products') }}</h1>
            <p>{{ $category?->description ?? 'Lightweight, durable eyewear in every shape and finish — authentic brands, nationwide delivery.' }}</p>
        </div>
        <span class="count-chip tnum">{{ $products->total() }} products</span>
    </div>

    <div class="layout">
        <aside class="filters-panel">
            <form method="GET" id="filterForm">
                @if (request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif
                <div class="fgroup">
                    <h4>Categories</h4>
                    <label class="checkrow"><a href="{{ route('products.index', request()->except(['page'])) }}" style="color:{{ ! $category ? 'var(--blue)' : 'inherit' }};font-weight:{{ ! $category ? 600 : 400 }}">All Products</a></label>
                    @foreach ($categories as $c)
                        <label class="checkrow"><a href="{{ route('category.show', $c) }}" style="color:{{ $category?->id === $c->id ? 'var(--blue)' : 'inherit' }};font-weight:{{ $category?->id === $c->id ? 600 : 400 }}">{{ $c->name }}</a></label>
                    @endforeach
                </div>

                <div class="fgroup">
                    <h4>Price Range (৳)</h4>
                    <div style="display:flex;gap:8px">
                        <input type="number" name="min" value="{{ request('min') }}" placeholder="Min" style="width:50%;border:1px solid var(--line);border-radius:8px;padding:8px">
                        <input type="number" name="max" value="{{ request('max') }}" placeholder="Max" style="width:50%;border:1px solid var(--line);border-radius:8px;padding:8px">
                    </div>
                </div>

                @if ($brands->isNotEmpty())
                <div class="fgroup">
                    <h4>Brand</h4>
                    @foreach ($brands as $brand)
                        <label class="checkrow"><a href="{{ route(request()->routeIs('category.show') ? 'category.show' : 'products.index', request()->routeIs('category.show') ? ['category' => $category, 'brand' => $brand] : ['brand' => $brand]) }}" style="color:{{ request('brand') === $brand ? 'var(--blue)' : 'inherit' }}">{{ $brand }}</a></label>
                    @endforeach
                </div>
                @endif

                <button type="submit" class="btn btn-primary btn-sm" style="width:100%">Apply Filters</button>
            </form>
        </aside>

        <div>
            <div class="toolbar">
                <span class="res tnum">Showing {{ $products->count() }} of {{ $products->total() }} results</span>
                <form method="GET" id="sortForm">
                    @foreach (request()->except(['sort', 'page']) as $k => $v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
                    <select class="sortsel" name="sort" aria-label="Sort by" onchange="document.getElementById('sortForm').submit()">
                        <option value="">Sort: Featured</option>
                        <option value="price_low" @selected(request('sort')==='price_low')>Price: Low to High</option>
                        <option value="price_high" @selected(request('sort')==='price_high')>Price: High to Low</option>
                        <option value="name" @selected(request('sort')==='name')>Name A–Z</option>
                    </select>
                </form>
            </div>

            <div class="prod-grid">
                @forelse ($products as $product)
                    @include('storefront.partials.product-card', ['product' => $product])
                @empty
                    <p class="sec-sub" style="grid-column:1/-1;padding:40px 0;text-align:center">No products found. Try adjusting your filters.</p>
                @endforelse
            </div>

            {{ $products->onEachSide(1)->links('vendor.pagination.storefront') }}
        </div>
    </div>
</div>
@endsection
