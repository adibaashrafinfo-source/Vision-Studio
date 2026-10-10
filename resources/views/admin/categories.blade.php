@extends('layouts.admin')

@section('title', 'Categories')

@section('content')
<div x-data="categoriesPage()">
    <div class="ph-head">
        <div><h1>Categories</h1><p>Manage product categories for your store. Add, edit and organize your categories.</p></div>
        <div class="ph-tools"><button class="btn btn-primary" @click="openAdd()"><svg class="icon sm" viewBox="0 0 24 24" style="stroke:#fff"><path d="M12 5v14"></path><path d="M5 12h14"></path></svg>Add Category</button></div>
    </div>

    <div class="stat-row s4">
        <div class="stat"><div class="top"><span class="ic b"><svg class="icon" viewBox="0 0 24 24"><rect x="3" y="3" width="8" height="8" rx="2"></rect><rect x="13" y="3" width="8" height="8" rx="2"></rect><rect x="3" y="13" width="8" height="8" rx="2"></rect><rect x="13" y="13" width="8" height="8" rx="2"></rect></svg></span><div><div class="v tnum">{{ $stats['total'] }}</div><div class="k">Total Categories</div></div></div></div>
        <div class="stat"><div class="top"><span class="ic g"><svg class="icon" viewBox="0 0 24 24"><path d="M3 7l9-4 9 4-9 4-9-4z"></path><path d="M3 7v10l9 4 9-4V7"></path></svg></span><div><div class="v tnum">{{ $stats['active'] }}</div><div class="k">Active Categories</div></div></div></div>
        <div class="stat"><div class="top"><span class="ic r"><svg class="icon" viewBox="0 0 24 24"><rect x="4" y="4" width="16" height="16" rx="3"></rect><path d="M9 9h2v6H9z"></path><path d="M13 9h2v6h-2z"></path></svg></span><div><div class="v tnum">{{ $stats['inactive'] }}</div><div class="k">Inactive Categories</div></div></div></div>
        <div class="stat"><div class="top"><span class="ic p"><svg class="icon" viewBox="0 0 24 24"><path d="M20.6 13.4L13 21a2 2 0 0 1-2.8 0l-7-7a2 2 0 0 1-.6-1.4V4h8.6a2 2 0 0 1 1.4.6l7 7a2 2 0 0 1 0 2.8z"></path><circle cx="7.5" cy="7.5" r="1.3"></circle></svg></span><div><div class="v tnum">{{ $stats['products'] }}</div><div class="k">Total Products</div></div></div></div>
    </div>

    <div class="pg-panel">
        <div class="card">
            <form class="tbar" method="GET" action="{{ route('admin.categories.index') }}">
                <div class="srch"><svg class="icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"></circle><path d="M21 21l-4-4"></path></svg><input name="q" value="{{ request('q') }}" placeholder="Search categories…"></div>
                <select class="sel" name="status" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="active" @selected(request('status')==='active')>Active</option>
                    <option value="inactive" @selected(request('status')==='inactive')>Inactive</option>
                </select>
            </form>
            <table class="tbl"><thead><tr><th>#</th><th>Image</th><th>Category Name</th><th>Slug</th><th>Products</th><th>Status</th><th>Position</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse ($categories as $category)
                    @php($editData = [
                        'id' => $category->id, 'name' => $category->name, 'slug' => $category->slug,
                        'description' => $category->description, 'parent_id' => $category->parent_id,
                        'position' => $category->position, 'status' => $category->status, 'icon' => $category->icon,
                        'image' => $category->image_path ? asset('storage/'.$category->image_path) : null,
                    ])
                    <tr>
                        <td>{{ $loop->iteration + ($categories->firstItem() ? $categories->firstItem() - 1 : 0) }}</td>
                        <td><div class="pthumb">
                            @if ($category->image_path)<img class="pimg" src="{{ asset('storage/'.$category->image_path) }}" alt="">
                            @else<svg class="icon" viewBox="0 0 24 24" style="stroke:var(--muted)"><rect x="3" y="3" width="8" height="8" rx="2"></rect><rect x="13" y="3" width="8" height="8" rx="2"></rect><rect x="3" y="13" width="8" height="8" rx="2"></rect><rect x="13" y="13" width="8" height="8" rx="2"></rect></svg>@endif
                        </div></td>
                        <td class="pname">{{ $category->name }}</td>
                        <td class="slug">{{ $category->slug }}</td>
                        <td class="tnum">{{ $category->products_count }}</td>
                        <td><span class="pillst {{ $category->status === 'active' ? 'done' : 'cancel' }}">{{ ucfirst($category->status) }}</span></td>
                        <td><div class="posctl"><span class="n">{{ $category->position }}</span></div></td>
                        <td><div class="acts">
                            <button type="button" class="ic-btn" title="Edit" @click="openEdit({{ Illuminate\Support\Js::from($editData) }})"><svg class="icon xs" viewBox="0 0 24 24"><path d="M12 20h9"></path><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"></path></svg></button>
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Delete this category?')" style="display:inline">
                                @csrf @method('DELETE')
                                <button class="ic-btn danger" title="Delete"><svg class="icon xs" viewBox="0 0 24 24"><path d="M4 7h16"></path><path d="M6 7l1 13h10l1-13"></path></svg></button>
                            </form>
                        </div></td>
                    </tr>
                @empty
                    <tr><td colspan="8" style="color:var(--muted);padding:22px 12px">No categories yet. Click “Add Category”.</td></tr>
                @endforelse
            </tbody></table>
            <div class="tbl-foot">
                <div class="pginfo">Showing {{ $categories->firstItem() ?? 0 }} to {{ $categories->lastItem() ?? 0 }} of {{ $categories->total() }} categories</div>
                <div class="pg">{{ $categories->links('vendor.pagination.admin') }}</div>
            </div>
        </div>

        <!-- add / edit category panel (docked) -->
        <div class="panel">
            <form method="POST" :action="formAction" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" :value="mode==='edit' ? 'PUT' : 'POST'">
                <div class="panel-h"><h3 x-text="mode==='edit' ? 'Edit Category' : 'Add New Category'"></h3><button type="button" class="panel-x" @click="reset()">✕</button></div>
                <div class="panel-b">
                    <div class="ptabs">
                        <button type="button" :class="tab==='basic' && 'on'" @click="tab='basic'">Basic Information</button>
                        <button type="button" :class="tab==='display' && 'on'" @click="tab='display'">Display</button>
                    </div>

                    <div x-show="tab==='basic'">
                        <div class="f2">
                            <div class="field"><label>Category Name <span class="req">*</span></label><input name="name" x-model="form.name" placeholder="Enter category name" required></div>
                            <div class="field"><label>Slug</label><div class="ipg"><input name="slug" x-model="form.slug" placeholder="auto-generated"></div></div>
                        </div>
                        <div class="field"><label>Short Description</label>
                            <div class="rte"><div class="rte-bar"><b>B</b><i>I</i><u>U</u></div><textarea name="description" x-model="form.description" placeholder="Enter short description…"></textarea></div>
                        </div>
                        <div class="f2">
                            <div class="field"><label>Category Image</label><label class="ubox" style="cursor:pointer"><svg class="icon" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="9" cy="9" r="2"></circle><path d="M21 15l-5-5L5 21"></path></svg><b>Click to upload image</b><span>Recommended 800×800 px</span><input type="file" name="image" accept="image/*" class="hide" @change="previewImage($event)"></label></div>
                            <div class="field"><label>Image Preview</label><div class="img-prev"><template x-if="form.image"><img :src="form.image"></template><template x-if="!form.image"><span style="color:var(--muted);font-size:11px">No image</span></template></div></div>
                        </div>
                        <div class="field"><label>Banner Image (Optional)</label><label class="ubox" style="cursor:pointer"><svg class="icon" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="9" cy="9" r="2"></circle><path d="M21 15l-5-5L5 21"></path></svg><b>Click to upload banner</b><span>Recommended 1920×600 px</span><input type="file" name="banner" accept="image/*" class="hide"></label></div>
                    </div>

                    <div x-show="tab==='display'" style="display:none">
                        <div class="f2">
                            <div class="field"><label>Parent Category</label><select name="parent_id" x-model="form.parent_id"><option value="">None (Main Category)</option>@foreach ($parents as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select></div>
                            <div class="field"><label>Display Order</label><input type="number" name="position" x-model="form.position" value="0"></div>
                        </div>
                        <div class="f2">
                            <div class="field"><label>Status</label><div class="radio-row"><label @click="form.status='active'"><span class="rd" :class="form.status==='active' && 'on'"></span>Active</label><label @click="form.status='inactive'"><span class="rd" :class="form.status==='inactive' && 'on'"></span>Inactive</label></div><input type="hidden" name="status" x-model="form.status"></div>
                            <div class="field"><label>Icon (emoji / name, optional)</label><input name="icon" x-model="form.icon" placeholder="👓"></div>
                        </div>
                    </div>
                </div>
                <div class="panel-f"><button type="button" class="btn btn-ghost" @click="reset()">Cancel</button><button type="submit" class="btn btn-primary" x-text="mode==='edit' ? 'Update Category' : 'Save Category'"></button></div>
            </form>
        </div>
    </div>
</div>

<script>
function categoriesPage() {
    return {
        tab: 'basic',
        mode: 'add',
        baseUrl: @json(route('admin.categories.index')),
        form: { id: null, name: '', slug: '', description: '', parent_id: '', position: 0, status: 'active', icon: '', image: null },
        get formAction() { return this.mode === 'edit' ? this.baseUrl + '/' + this.form.id : this.baseUrl; },
        openAdd() { this.reset(); this.mode = 'add'; },
        openEdit(cat) {
            this.mode = 'edit'; this.tab = 'basic';
            this.form = {
                id: cat.id, name: cat.name ?? '', slug: cat.slug ?? '', description: cat.description ?? '',
                parent_id: cat.parent_id ?? '', position: cat.position ?? 0, status: cat.status ?? 'active',
                icon: cat.icon ?? '', image: cat.image ?? null,
            };
        },
        reset() {
            this.mode = 'add'; this.tab = 'basic';
            this.form = { id: null, name: '', slug: '', description: '', parent_id: '', position: 0, status: 'active', icon: '', image: null };
        },
        previewImage(e) {
            const file = e.target.files[0];
            if (file) this.form.image = URL.createObjectURL(file);
        },
    };
}
</script>
@endsection
