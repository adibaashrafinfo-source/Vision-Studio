@extends('layouts.admin')

@section('title', 'Coupons')

@section('content')
<div x-data="couponsPage()">
    <div class="ph-head">
        <div><h1>Coupons Management</h1><p>Create and track discount codes to drive sales across your store.</p></div>
        <div class="ph-tools"><button class="btn btn-primary" @click="openAdd()"><svg class="icon sm" viewBox="0 0 24 24" style="stroke:#fff"><path d="M12 5v14"></path><path d="M5 12h14"></path></svg>Add Coupon</button></div>
    </div>

    <div class="stat-row s4">
        <div class="stat"><div class="top"><span class="ic b"><svg class="icon" viewBox="0 0 24 24"><path d="M3 9a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v2a2 2 0 0 0 0 4v2a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-2a2 2 0 0 0 0-4z"></path></svg></span><div><div class="v tnum">{{ $stats['total'] }}</div><div class="k">Total Coupons</div></div></div></div>
        <div class="stat"><div class="top"><span class="ic g"><svg class="icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"></circle><path d="M8 12l3 3 5-6"></path></svg></span><div><div class="v tnum">{{ $stats['active'] }}</div><div class="k">Active Coupons</div></div></div></div>
        <div class="stat"><div class="top"><span class="ic p"><svg class="icon" viewBox="0 0 24 24"><path d="M3 12l5-6 4 4 9-9"></path></svg></span><div><div class="v tnum">{{ $stats['used'] }}</div><div class="k">Total Usages</div></div></div></div>
        <div class="stat"><div class="top"><span class="ic a"><svg class="icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path></svg></span><div><div class="v tnum">{{ $stats['expired'] }}</div><div class="k">Expired Coupons</div></div></div></div>
    </div>

    <div class="pg-panel">
        <div class="card">
            <form class="tbar" method="GET" action="{{ route('admin.coupons.index') }}">
                <div class="srch"><svg class="icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"></circle><path d="M21 21l-4-4"></path></svg><input name="q" value="{{ request('q') }}" placeholder="Search coupon code…"></div>
                <select class="sel" name="status" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="active" @selected(request('status')==='active')>Active</option>
                    <option value="inactive" @selected(request('status')==='inactive')>Inactive</option>
                </select>
            </form>
            <table class="tbl"><thead><tr><th>Code</th><th>Description</th><th>Type</th><th>Discount</th><th>Usage</th><th>Valid Period</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse ($coupons as $coupon)
                    <tr>
                        <td><span class="oid">{{ $coupon->code }}</span></td>
                        <td class="pcat">{{ \Illuminate\Support\Str::limit($coupon->title ?: $coupon->description, 40) }}</td>
                        <td>{{ ucfirst($coupon->type) }}</td>
                        <td class="tnum" style="font-weight:700">{{ $coupon->type === 'percent' ? $coupon->value.'%' : taka($coupon->value) }}</td>
                        <td class="tnum">{{ $coupon->used_count }}{{ $coupon->usage_limit ? ' / '.$coupon->usage_limit : '' }}</td>
                        <td class="pcat">{{ $coupon->starts_at?->format('M d') ?? '—' }} – {{ $coupon->ends_at?->format('M d, Y') ?? '∞' }}</td>
                        <td><span class="pillst {{ $coupon->status === 'active' ? 'done' : 'cancel' }}">{{ ucfirst($coupon->status) }}</span></td>
                        <td><div class="acts">
                            <button type="button" class="ic-btn" title="Edit" @click="openEdit({{ Illuminate\Support\Js::from([
                                'id'=>$coupon->id,'code'=>$coupon->code,'title'=>$coupon->title,'description'=>$coupon->description,
                                'type'=>$coupon->type,'value'=>$coupon->value,'min_order'=>$coupon->min_order,'max_discount'=>$coupon->max_discount,
                                'usage_limit'=>$coupon->usage_limit,'status'=>$coupon->status,
                                'starts_at'=>$coupon->starts_at?->format('Y-m-d'),'ends_at'=>$coupon->ends_at?->format('Y-m-d'),
                            ]) }})"><svg class="icon xs" viewBox="0 0 24 24"><path d="M12 20h9"></path><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"></path></svg></button>
                            <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" onsubmit="return confirm('Delete this coupon?')" style="display:inline">
                                @csrf @method('DELETE')
                                <button class="ic-btn danger"><svg class="icon xs" viewBox="0 0 24 24"><path d="M4 7h16"></path><path d="M6 7l1 13h10l1-13"></path></svg></button>
                            </form>
                        </div></td>
                    </tr>
                @empty
                    <tr><td colspan="8" style="color:var(--muted);padding:22px 12px">No coupons yet.</td></tr>
                @endforelse
            </tbody></table>
            <div class="tbl-foot">
                <div class="pginfo">Showing {{ $coupons->firstItem() ?? 0 }} to {{ $coupons->lastItem() ?? 0 }} of {{ $coupons->total() }} coupons</div>
                <div class="pg">{{ $coupons->links('vendor.pagination.admin') }}</div>
            </div>
        </div>

        <div class="panel">
            <form method="POST" :action="formAction">
                @csrf
                <input type="hidden" name="_method" :value="mode==='edit' ? 'PUT' : 'POST'">
                <div class="panel-h"><h3 x-text="mode==='edit' ? 'Edit Coupon' : 'Create Coupon'"></h3><button type="button" class="panel-x" @click="reset()">✕</button></div>
                <div class="panel-b">
                    <div class="field"><label>Coupon Code <span class="req">*</span></label><input name="code" x-model="form.code" style="text-transform:uppercase" placeholder="SUMMER20" required></div>
                    <div class="field"><label>Title</label><input name="title" x-model="form.title" placeholder="Summer Sale"></div>
                    <div class="field"><label>Description</label><textarea name="description" x-model="form.description"></textarea></div>
                    <div class="f2">
                        <div class="field"><label>Type <span class="req">*</span></label><select name="type" x-model="form.type"><option value="percent">Percentage (%)</option><option value="fixed">Fixed (৳)</option></select></div>
                        <div class="field"><label>Value <span class="req">*</span></label><input type="number" step="0.01" name="value" x-model="form.value" required></div>
                    </div>
                    <div class="f2">
                        <div class="field"><label>Min Order (৳)</label><input type="number" step="0.01" name="min_order" x-model="form.min_order"></div>
                        <div class="field"><label>Max Discount (৳)</label><input type="number" step="0.01" name="max_discount" x-model="form.max_discount"></div>
                    </div>
                    <div class="field"><label>Usage Limit</label><input type="number" name="usage_limit" x-model="form.usage_limit" placeholder="unlimited"></div>
                    <div class="f2">
                        <div class="field"><label>Starts At</label><input type="date" name="starts_at" x-model="form.starts_at"></div>
                        <div class="field"><label>Ends At</label><input type="date" name="ends_at" x-model="form.ends_at"></div>
                    </div>
                    <div class="field"><label>Status</label><select name="status" x-model="form.status"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
                </div>
                <div class="panel-f"><button type="button" class="btn btn-ghost" @click="reset()">Cancel</button><button type="submit" class="btn btn-primary" x-text="mode==='edit' ? 'Update Coupon' : 'Save Coupon'"></button></div>
            </form>
        </div>
    </div>
</div>

<script>
function couponsPage() {
    const blank = { id:null, code:'', title:'', description:'', type:'percent', value:'', min_order:'', max_discount:'', usage_limit:'', starts_at:'', ends_at:'', status:'active' };
    return {
        mode: 'add',
        baseUrl: @json(route('admin.coupons.index')),
        form: { ...blank },
        get formAction() { return this.mode === 'edit' ? this.baseUrl + '/' + this.form.id : this.baseUrl; },
        openAdd() { this.reset(); },
        openEdit(c) { this.mode='edit'; this.form = { ...blank, ...c, value:c.value??'', min_order:c.min_order??'', max_discount:c.max_discount??'', usage_limit:c.usage_limit??'', starts_at:c.starts_at??'', ends_at:c.ends_at??'' }; },
        reset() { this.mode='add'; this.form = { ...blank }; },
    };
}
</script>
@endsection
