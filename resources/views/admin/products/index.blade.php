@extends('layouts.admin')

@section('title', 'Products')

@section('content')
    <div class="ph-head">
        <div><h1>Products</h1><p>Manage all your products, add new products, edit or update existing ones.</p></div>
        <div class="ph-tools">
            <a class="btn btn-primary" href="{{ route('admin.products.create') }}"><svg class="icon sm" viewBox="0 0 24 24" style="stroke:#fff"><path d="M12 5v14"></path><path d="M5 12h14"></path></svg>Add Product</a>
        </div>
    </div>

    <div class="stat-row s5">
        <div class="stat"><div class="top"><span class="ic b"><svg class="icon" viewBox="0 0 24 24"><path d="M3 7l9-4 9 4-9 4-9-4z"></path><path d="M3 7v10l9 4 9-4V7"></path></svg></span><div><div class="v tnum">{{ $stats['total'] }}</div><div class="k">Total Products</div></div></div></div>
        <div class="stat"><div class="top"><span class="ic g"><svg class="icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"></circle><path d="M8 12l3 3 5-6"></path></svg></span><div><div class="v tnum">{{ $stats['active'] }}</div><div class="k">Active Products</div></div></div></div>
        <div class="stat"><div class="top"><span class="ic r"><svg class="icon" viewBox="0 0 24 24"><rect x="4" y="4" width="16" height="16" rx="3"></rect><path d="M9 9h2v6H9z"></path><path d="M13 9h2v6h-2z"></path></svg></span><div><div class="v tnum">{{ $stats['inactive'] }}</div><div class="k">Inactive Products</div></div></div></div>
        <div class="stat"><div class="top"><span class="ic p"><svg class="icon" viewBox="0 0 24 24"><path d="M12 3l2.6 5.5 6 .8-4.4 4.1 1.1 6-5.3-2.9-5.3 2.9 1.1-6L3.4 9.3l6-.8z"></path></svg></span><div><div class="v tnum">{{ $stats['featured'] }}</div><div class="k">Featured Products</div></div></div></div>
        <div class="stat"><div class="top"><span class="ic a"><svg class="icon" viewBox="0 0 24 24"><path d="M12 9v4"></path><path d="M12 17h.01"></path><path d="M10.3 3.9l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"></path></svg></span><div><div class="v tnum">{{ $stats['low'] }}</div><div class="k">Low Stock</div></div></div></div>
    </div>

    <div class="card">
        <form class="tbar" method="GET" action="{{ route('admin.products.index') }}">
            <div class="srch"><svg class="icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"></circle><path d="M21 21l-4-4"></path></svg><input name="q" value="{{ request('q') }}" placeholder="Search products…"></div>
            <select class="sel" name="category" onchange="this.form.submit()">
                <option value="">All Categories</option>
                @foreach ($categories as $c)<option value="{{ $c->id }}" @selected(request('category')==$c->id)>{{ $c->name }}</option>@endforeach
            </select>
            <select class="sel" name="status" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="active" @selected(request('status')==='active')>Active</option>
                <option value="draft" @selected(request('status')==='draft')>Draft</option>
                <option value="inactive" @selected(request('status')==='inactive')>Inactive</option>
            </select>
        </form>
        <table class="tbl"><thead><tr><th>Product</th><th>Category</th><th>Brand</th><th>Price</th><th>Stock</th><th>Status</th><th>Featured</th><th></th></tr></thead>
        <tbody>
            @forelse ($products as $product)
                <tr>
                    <td><div class="prow">
                        <div class="pthumb">
                            @if ($product->primaryImage)<img class="pimg" src="{{ asset('storage/'.$product->primaryImage->image_path) }}" alt="">
                            @else<svg class="icon" viewBox="0 0 24 24" style="stroke:var(--muted)"><path d="M3 7l9-4 9 4-9 4-9-4z"></path><path d="M3 7v10l9 4 9-4V7"></path></svg>@endif
                        </div>
                        <div><div class="pname">{{ $product->name }}</div><div class="pcat">SKU: {{ $product->sku ?: '—' }}</div></div>
                    </div></td>
                    <td>{{ $product->category?->name ?? '—' }}</td>
                    <td>{{ $product->brand ?: '—' }}</td>
                    <td class="tnum">{{ taka($product->price) }}</td>
                    <td style="font-weight:700;color:{{ $product->stock_qty <= 5 ? 'var(--sale)' : 'var(--green)' }}">{{ $product->stock_qty }}</td>
                    <td><span class="pillst {{ $product->status === 'active' ? 'done' : 'cancel' }}">{{ ucfirst($product->status) }}</span></td>
                    <td>
                        <form method="POST" action="{{ route('admin.products.feature', $product) }}" style="margin:0">
                            @csrf @method('PUT')
                            <button type="submit" class="tw {{ $product->featured ? 'on' : '' }}" style="border:0;padding:0" title="Toggle featured"><i></i></button>
                        </form>
                    </td>
                    <td><div class="acts">
                        <a class="ic-btn" href="{{ route('admin.products.edit', $product) }}" title="Edit"><svg class="icon xs" viewBox="0 0 24 24"><path d="M12 20h9"></path><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"></path></svg></a>
                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Delete this product?')" style="display:inline">
                            @csrf @method('DELETE')
                            <button class="ic-btn danger" title="Delete"><svg class="icon xs" viewBox="0 0 24 24"><path d="M4 7h16"></path><path d="M6 7l1 13h10l1-13"></path></svg></button>
                        </form>
                    </div></td>
                </tr>
            @empty
                <tr><td colspan="8" style="color:var(--muted);padding:22px 12px">No products yet. Click “Add Product”.</td></tr>
            @endforelse
        </tbody></table>
        <div class="tbl-foot">
            <div class="pginfo">Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} products</div>
            <div class="pg">{{ $products->links('vendor.pagination.admin') }}</div>
        </div>
    </div>
@endsection
