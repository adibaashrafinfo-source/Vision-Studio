@extends('layouts.admin')

@section('title', $product->exists ? 'Edit Product' : 'Add Product')

@php
    $variantData = old('variants', ($product->variants ?? collect())
        ->map(fn ($v) => ['name' => $v->name, 'value' => $v->value, 'price_delta' => $v->price_delta, 'stock_qty' => $v->stock_qty])
        ->values()->all());
@endphp

@section('content')
<div x-data="productForm()">
    <div class="ph-head">
        <div><h1>{{ $product->exists ? 'Edit Product' : 'Add Product' }}</h1><p>{{ $product->exists ? 'Update the details of this product.' : 'Create a new product for your store.' }}</p></div>
        <div class="ph-tools"><a class="btn btn-ghost" href="{{ route('admin.products.index') }}">← Back to Products</a></div>
    </div>

    <form method="POST" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" enctype="multipart/form-data">
        @csrf
        @if ($product->exists) @method('PUT') @endif

        <div class="card">
            <div style="display:grid;grid-template-columns:150px 1fr;gap:18px">
                <div class="ap-nav">
                    <button type="button" :class="tab==='basic' && 'on'" @click="tab='basic'"><svg class="icon" viewBox="0 0 24 24"><path d="M5 4h14v16H5z"></path><path d="M8 9h8"></path><path d="M8 13h5"></path></svg>Basic Info</button>
                    <button type="button" :class="tab==='pricing' && 'on'" @click="tab='pricing'"><svg class="icon" viewBox="0 0 24 24"><path d="M12 1v22"></path><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>Pricing &amp; Stock</button>
                    <button type="button" :class="tab==='images' && 'on'" @click="tab='images'"><svg class="icon" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="9" cy="9" r="2"></circle><path d="M21 15l-5-5L5 21"></path></svg>Images</button>
                    <button type="button" :class="tab==='variants' && 'on'" @click="tab='variants'"><svg class="icon" viewBox="0 0 24 24"><rect x="4" y="4" width="7" height="7"></rect><rect x="13" y="4" width="7" height="7"></rect><rect x="4" y="13" width="7" height="7"></rect></svg>Variants</button>
                    <button type="button" :class="tab==='status' && 'on'" @click="tab='status'"><svg class="icon" viewBox="0 0 24 24"><path d="M9 11l3 3 8-8"></path><path d="M20 12v7H5V5h9"></path></svg>Status</button>
                </div>

                <div>
                    {{-- Basic Info --}}
                    <div x-show="tab==='basic'">
                        <div class="f2">
                            <div class="field"><label>Product Name <span class="req">*</span></label><input name="name" value="{{ old('name', $product->name) }}" required></div>
                            <div class="field"><label>SKU</label><input name="sku" value="{{ old('sku', $product->sku) }}"></div>
                        </div>
                        <div class="f2">
                            <div class="field"><label>Category</label><select name="category_id"><option value="">— Select —</option>@foreach ($categories as $c)<option value="{{ $c->id }}" @selected(old('category_id', $product->category_id)==$c->id)>{{ $c->name }}</option>@endforeach</select></div>
                            <div class="field"><label>Brand</label><input name="brand" value="{{ old('brand', $product->brand) }}" placeholder="e.g. Ray-Ban"></div>
                        </div>
                        <div class="field"><label>Short Description</label><div class="rte"><div class="rte-bar"><b>B</b><i>I</i><u>U</u></div><textarea name="short_desc">{{ old('short_desc', $product->short_desc) }}</textarea></div></div>
                        <div class="field"><label>Full Description</label><div class="rte"><div class="rte-bar"><b>B</b><i>I</i><u>U</u></div><textarea name="long_desc" style="min-height:120px">{{ old('long_desc', $product->long_desc) }}</textarea></div></div>
                    </div>

                    {{-- Pricing & Stock --}}
                    <div x-show="tab==='pricing'" style="display:none">
                        <div class="f2">
                            <div class="field"><label>Price (৳) <span class="req">*</span></label><input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}" required></div>
                            <div class="field"><label>Compare-at Price (৳)</label><input type="number" step="0.01" name="compare_price" value="{{ old('compare_price', $product->compare_price) }}" placeholder="optional"></div>
                        </div>
                        <div class="field" style="max-width:240px"><label>Stock Quantity <span class="req">*</span></label><input type="number" name="stock_qty" value="{{ old('stock_qty', $product->stock_qty ?? 0) }}" required></div>
                    </div>

                    {{-- Images --}}
                    <div x-show="tab==='images'" style="display:none">
                        <div class="field"><label>Product Images</label>
                            <div class="gal">
                                @foreach ($product->images ?? [] as $img)
                                    <div class="g {{ $img->is_primary ? 'main' : '' }}">
                                        <img class="pimg" src="{{ asset('storage/'.$img->image_path) }}" alt="">
                                        <form method="POST" action="{{ route('admin.products.images.destroy', [$product, $img]) }}" onsubmit="return confirm('Remove this image?')" style="position:absolute;top:5px;right:5px;z-index:1">
                                            @csrf @method('DELETE')
                                            <button type="submit" title="Remove image" style="width:20px;height:20px;border-radius:50%;background:var(--surface);border:1px solid var(--line);display:flex;align-items:center;justify-content:center;font-size:11px;color:var(--muted);cursor:pointer;line-height:1">✕</button>
                                        </form>
                                    </div>
                                @endforeach
                                <template x-for="(p,i) in previews" :key="p.id">
                                    <div class="g">
                                        <img class="pimg" :src="p.url">
                                        <button type="button" title="Remove image" @click="removeImage(i)" style="position:absolute;top:5px;right:5px;z-index:1;width:20px;height:20px;border-radius:50%;background:var(--surface);border:1px solid var(--line);display:flex;align-items:center;justify-content:center;font-size:11px;color:var(--muted);cursor:pointer;line-height:1">✕</button>
                                    </div>
                                </template>
                                <label class="g add" style="cursor:pointer"><svg class="icon sm" viewBox="0 0 24 24"><path d="M12 5v14"></path><path d="M5 12h14"></path></svg>Upload Images
                                    <input type="file" name="images[]" accept="image/*" multiple class="hide" x-ref="imageInput" @change="addImages($event)">
                                </label>
                            </div>
                            <p class="sec-sub" style="margin-top:10px">You can add several images — pick more than one at a time, or click "Upload Images" again to add more. The first image becomes the primary image if none is set. Recommended 800×800px.</p>
                        </div>

                        <div class="field">
                            <label>Product Video</label>
                            @if ($product->video_path)
                                <div class="drow" style="align-items:center;gap:12px;margin-bottom:10px">
                                    <video src="{{ asset('storage/'.$product->video_path) }}" controls style="width:220px;border-radius:8px;background:#000"></video>
                                    <form method="POST" action="{{ route('admin.products.video.destroy', $product) }}" onsubmit="return confirm('Remove this video?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-ghost">Remove video</button>
                                    </form>
                                </div>
                            @endif
                            <label class="g add" style="cursor:pointer;width:160px;height:90px">
                                <svg class="icon sm" viewBox="0 0 24 24"><path d="M23 7l-7 5 7 5V7z"></path><rect x="1" y="5" width="15" height="14" rx="2"></rect></svg>
                                {{ $product->video_path ? 'Replace video' : 'Upload video' }}
                                <input type="file" name="video" accept="video/mp4,video/quicktime,video/webm" class="hide">
                            </label>
                            <p class="sec-sub" style="margin-top:10px">Optional. MP4, MOV or WebM, up to 50MB. Shown on the product detail page alongside the images.</p>
                        </div>
                    </div>

                    {{-- Variants --}}
                    <div x-show="tab==='variants'" style="display:none">
                        <p class="sec-sub">Add frame colors, lens types or other options. Leave empty for a simple product.</p>
                        <template x-for="(v,i) in variants" :key="i">
                            <div class="drow" style="margin-bottom:10px">
                                <input :name="`variants[${i}][name]`" x-model="v.name" placeholder="Option name (e.g. Frame Color)">
                                <input :name="`variants[${i}][value]`" x-model="v.value" placeholder="Value (e.g. Matte Black)">
                                <input :name="`variants[${i}][price_delta]`" x-model="v.price_delta" type="number" step="0.01" placeholder="+৳0" style="max-width:90px">
                                <input :name="`variants[${i}][stock_qty]`" x-model="v.stock_qty" type="number" placeholder="Stock" style="max-width:80px">
                                <button type="button" class="ic-btn danger" @click="variants.splice(i,1)"><svg class="icon xs" viewBox="0 0 24 24"><path d="M4 7h16"></path><path d="M6 7l1 13h10l1-13"></path></svg></button>
                            </div>
                        </template>
                        <button type="button" class="chip" @click="variants.push({name:'',value:'',price_delta:0,stock_qty:0})"><svg class="icon xs" viewBox="0 0 24 24"><path d="M12 5v14"></path><path d="M5 12h14"></path></svg>Add Variant</button>
                    </div>

                    {{-- Status --}}
                    <div x-show="tab==='status'" style="display:none">
                        <div class="field" style="max-width:260px"><label>Status</label>
                            <select name="status">
                                <option value="active" @selected(old('status', $product->status)==='active')>Active</option>
                                <option value="draft" @selected(old('status', $product->status)==='draft')>Draft</option>
                                <option value="inactive" @selected(old('status', $product->status)==='inactive')>Inactive</option>
                            </select>
                        </div>
                        <label style="display:flex;align-items:center;gap:10px;font-size:13px;font-weight:600;margin-top:10px;cursor:pointer" @click.prevent="featured=!featured">
                            <span class="tw" :class="featured && 'on'"><i></i></span> Featured product
                            <input type="hidden" name="featured" :value="featured ? 1 : 0">
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="save-bar" style="gap:10px">
            <a class="btn btn-ghost" href="{{ route('admin.products.index') }}">Cancel</a>
            <button type="submit" class="btn btn-primary">{{ $product->exists ? 'Update Product' : 'Publish Product' }}</button>
        </div>
    </form>
</div>

<script>
function productForm() {
    return {
        tab: 'basic',
        previews: [],
        _files: [],
        _seq: 0,
        featured: {{ old('featured', $product->featured) ? 'true' : 'false' }},
        variants: {{ Illuminate\Support\Js::from($variantData) }},
        addImages(e) {
            for (const f of Array.from(e.target.files)) {
                this._files.push(f);
                this.previews.push({ id: ++this._seq, url: URL.createObjectURL(f) });
            }
            this.syncInput();
        },
        removeImage(i) {
            URL.revokeObjectURL(this.previews[i].url);
            this._files.splice(i, 1);
            this.previews.splice(i, 1);
            this.syncInput();
        },
        syncInput() {
            const dt = new DataTransfer();
            this._files.forEach(f => dt.items.add(f));
            this.$refs.imageInput.files = dt.files;
        },
    };
}
</script>
@endsection
