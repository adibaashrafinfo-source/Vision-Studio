@extends('layouts.admin')

@section('title', 'Customers')

@php $avColors = ['', 'c2', 'c3', 'c4', 'c5']; @endphp

@section('content')
<div x-data="customersPage()">
    <div class="ph-head">
        <div><h1>Customers</h1><p>View, segment and manage every customer who shops at {{ config('app.name') }}.</p></div>
        <div class="ph-tools"><button class="btn btn-primary" @click="openAdd()"><svg class="icon sm" viewBox="0 0 24 24" style="stroke:#fff"><path d="M12 5v14"></path><path d="M5 12h14"></path></svg>Add Customer</button></div>
    </div>

    <div class="stat-row s4">
        <div class="stat"><div class="top"><span class="ic b"><svg class="icon" viewBox="0 0 24 24"><circle cx="9" cy="8" r="3.5"></circle><path d="M2 20c0-3.5 3-6 7-6s7 2.5 7 6"></path></svg></span><div><div class="v tnum">{{ $stats['total'] }}</div><div class="k">Total Customers</div></div></div></div>
        <div class="stat"><div class="top"><span class="ic g"><svg class="icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"></circle><path d="M8 12l3 3 5-6"></path></svg></span><div><div class="v tnum">{{ $stats['active'] }}</div><div class="k">Active Customers</div></div></div></div>
        <div class="stat"><div class="top"><span class="ic p"><svg class="icon" viewBox="0 0 24 24"><path d="M12 5v14"></path><path d="M5 12h14"></path></svg></span><div><div class="v tnum">{{ $stats['new'] }}</div><div class="k">New This Month</div></div></div></div>
        <div class="stat"><div class="top"><span class="ic r"><svg class="icon" viewBox="0 0 24 24"><rect x="4" y="4" width="16" height="16" rx="3"></rect><path d="M9 9h2v6H9z"></path><path d="M13 9h2v6h-2z"></path></svg></span><div><div class="v tnum">{{ $stats['inactive'] }}</div><div class="k">Inactive Customers</div></div></div></div>
    </div>

    <div class="pg-panel">
        <div class="card">
            <form class="tbar" method="GET" action="{{ route('admin.customers.index') }}">
                <div class="srch"><svg class="icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"></circle><path d="M21 21l-4-4"></path></svg><input name="q" value="{{ request('q') }}" placeholder="Search name, email, phone…"></div>
                <select class="sel" name="status" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="active" @selected(request('status')==='active')>Active</option>
                    <option value="inactive" @selected(request('status')==='inactive')>Inactive</option>
                </select>
            </form>
            <table class="tbl"><thead><tr><th>Customer</th><th>Contact</th><th>Location</th><th>Orders</th><th>Total Spent</th><th>Status</th><th>Joined</th><th></th></tr></thead>
            <tbody>
                @forelse ($customers as $customer)
                    @php $init = collect(explode(' ', $customer->name))->take(2)->map(fn($w)=>mb_strtoupper(mb_substr($w,0,1)))->implode(''); @endphp
                    <tr>
                        <td><div class="cust"><div class="cav {{ $avColors[$customer->id % 5] }}">{{ $init }}</div><div><div class="pname">{{ $customer->name }}</div><div class="pcat">{{ ucfirst($customer->customer_group ?? 'regular') }}</div></div></div></td>
                        <td><div style="font-size:12.5px">{{ $customer->email }}<div class="pcat">{{ $customer->phone }}</div></div></td>
                        <td>{{ $customer->location_city ?: '—' }}</td>
                        <td class="tnum">{{ $customer->orders_count }}</td>
                        <td class="tnum" style="font-weight:700">{{ taka($customer->orders_sum_total ?? 0) }}</td>
                        <td><span class="pillst {{ $customer->status === 'active' ? 'done' : 'cancel' }}">{{ ucfirst($customer->status) }}</span></td>
                        <td class="pcat">{{ $customer->created_at->format('M d, Y') }}</td>
                        <td><div class="acts">
                            <a class="ic-btn" href="{{ route('admin.customers.show', $customer) }}" title="View"><svg class="icon xs" viewBox="0 0 24 24"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"></path><circle cx="12" cy="12" r="3"></circle></svg></a>
                            <button type="button" class="ic-btn" title="Edit" @click="openEdit({{ Illuminate\Support\Js::from([
                                'id'=>$customer->id,'name'=>$customer->name,'email'=>$customer->email,'phone'=>$customer->phone,
                                'location_city'=>$customer->location_city,'gender'=>$customer->gender,'customer_group'=>$customer->customer_group,
                                'notes'=>$customer->notes,'status'=>$customer->status,
                            ]) }})"><svg class="icon xs" viewBox="0 0 24 24"><path d="M12 20h9"></path><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"></path></svg></button>
                            <form method="POST" action="{{ route('admin.customers.destroy', $customer) }}" onsubmit="return confirm('Delete this customer?')" style="display:inline">
                                @csrf @method('DELETE')
                                <button class="ic-btn danger" title="Delete"><svg class="icon xs" viewBox="0 0 24 24"><path d="M4 7h16"></path><path d="M6 7l1 13h10l1-13"></path></svg></button>
                            </form>
                        </div></td>
                    </tr>
                @empty
                    <tr><td colspan="8" style="color:var(--muted);padding:22px 12px">No customers yet.</td></tr>
                @endforelse
            </tbody></table>
            <div class="tbl-foot">
                <div class="pginfo">Showing {{ $customers->firstItem() ?? 0 }} to {{ $customers->lastItem() ?? 0 }} of {{ $customers->total() }} customers</div>
                <div class="pg">{{ $customers->links('vendor.pagination.admin') }}</div>
            </div>
        </div>

        <div class="panel">
            <form method="POST" :action="formAction">
                @csrf
                <input type="hidden" name="_method" :value="mode==='edit' ? 'PUT' : 'POST'">
                <div class="panel-h"><h3 x-text="mode==='edit' ? 'Edit Customer' : 'Add Customer'"></h3><button type="button" class="panel-x" @click="reset()">✕</button></div>
                <div class="panel-b">
                    <div class="f2"><div class="field"><label>Name <span class="req">*</span></label><input name="name" x-model="form.name" required></div><div class="field"><label>Phone</label><input name="phone" x-model="form.phone"></div></div>
                    <div class="field"><label>Email <span class="req">*</span></label><input type="email" name="email" x-model="form.email" required></div>
                    <div class="f2"><div class="field"><label>City</label><input name="location_city" x-model="form.location_city"></div><div class="field"><label>Group</label><select name="customer_group" x-model="form.customer_group"><option value="regular">Regular</option><option value="vip">VIP</option><option value="wholesale">Wholesale</option></select></div></div>
                    <div class="f2"><div class="field"><label>Gender</label><select name="gender" x-model="form.gender"><option value="">—</option><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select></div><div class="field"><label>Status</label><select name="status" x-model="form.status"><option value="active">Active</option><option value="inactive">Inactive</option></select></div></div>
                    <div class="field"><label>Password <span x-show="mode==='edit'" class="pcat">(leave blank to keep)</span></label><input type="password" name="password" autocomplete="new-password"></div>
                    <div class="field"><label>Notes</label><textarea name="notes" x-model="form.notes"></textarea></div>
                </div>
                <div class="panel-f"><button type="button" class="btn btn-ghost" @click="reset()">Cancel</button><button type="submit" class="btn btn-primary" x-text="mode==='edit' ? 'Update' : 'Save'"></button></div>
            </form>
        </div>
    </div>
</div>

<script>
function customersPage() {
    return {
        mode: 'add',
        baseUrl: @json(route('admin.customers.index')),
        form: { id:null, name:'', email:'', phone:'', location_city:'', gender:'', customer_group:'regular', notes:'', status:'active' },
        get formAction() { return this.mode === 'edit' ? this.baseUrl + '/' + this.form.id : this.baseUrl; },
        openAdd() { this.reset(); },
        openEdit(c) { this.mode='edit'; this.form = { id:c.id, name:c.name??'', email:c.email??'', phone:c.phone??'', location_city:c.location_city??'', gender:c.gender??'', customer_group:c.customer_group??'regular', notes:c.notes??'', status:c.status??'active' }; },
        reset() { this.mode='add'; this.form = { id:null, name:'', email:'', phone:'', location_city:'', gender:'', customer_group:'regular', notes:'', status:'active' }; },
    };
}
</script>
@endsection
