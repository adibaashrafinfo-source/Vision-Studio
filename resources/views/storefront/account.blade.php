@extends('layouts.storefront')

@section('title', 'My Account')

@push('styles')<link rel="stylesheet" href="{{ asset('storefront/account.css') }}">@endpush

@php $pill = ['delivered'=>'done','processing'=>'proc','shipped'=>'ship','pending'=>'pend','cancelled'=>'cancel']; @endphp

@section('content')
<div class="wrap" x-data="{ tab: 'dash' }">
    <div style="padding:26px 0 8px"><h1 style="font-size:28px">Welcome back, {{ strtok($customer->name, ' ') }}</h1><p class="sec-sub">Member since {{ $customer->created_at->format('Y') }} · {{ ucfirst($customer->customer_group ?? 'regular') }}</p></div>

    <div class="acc-layout">
        <aside class="side">
            <button :class="tab==='dash' && 'on'" @click="tab='dash'"><svg class="icon" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"></rect><rect x="14" y="3" width="7" height="7" rx="1.5"></rect><rect x="3" y="14" width="7" height="7" rx="1.5"></rect><rect x="14" y="14" width="7" height="7" rx="1.5"></rect></svg>Dashboard</button>
            <button :class="tab==='orders' && 'on'" @click="tab='orders'"><svg class="icon" viewBox="0 0 24 24"><path d="M6 2h9l5 5v15H6z"></path><path d="M14 2v6h6"></path><path d="M9 13h7"></path><path d="M9 17h7"></path></svg>My Orders</button>
            <button :class="tab==='addr' && 'on'" @click="tab='addr'"><svg class="icon" viewBox="0 0 24 24"><path d="M12 21s-7-6-7-11a7 7 0 0 1 14 0c0 5-7 11-7 11z"></path><circle cx="12" cy="10" r="2.5"></circle></svg>Addresses</button>
            <button :class="tab==='profile' && 'on'" @click="tab='profile'"><svg class="icon" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"></circle><path d="M4 20c0-4 4-6 8-6s8 2 8 6"></path></svg>Profile</button>
            <form method="POST" action="{{ route('customer.logout') }}">@csrf<button class="logout" type="submit"><svg class="icon" viewBox="0 0 24 24"><path d="M15 12H4"></path><path d="M8 8l-4 4 4 4"></path><path d="M14 4h5v16h-5"></path></svg>Logout</button></form>
        </aside>

        <div>
            {{-- Dashboard --}}
            <div class="main-card" x-show="tab==='dash'">
                <h2>Dashboard</h2>
                <div class="stats">
                    <div class="stat"><div class="k">Total Orders</div><div class="v tnum">{{ $customer->orders->count() }}</div></div>
                    <div class="stat"><div class="k">Delivered</div><div class="v tnum">{{ $customer->orders->where('status','delivered')->count() }}</div></div>
                    <div class="stat"><div class="k">Total Spent</div><div class="v tnum">{{ taka($customer->orders->where('status','!=','cancelled')->sum('total')) }}</div></div>
                </div>
                <h2 style="font-size:18px">Recent Orders</h2>
                <table class="otable">
                    <thead><tr><th>Order</th><th>Date</th><th>Total</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse ($customer->orders->take(5) as $order)
                            <tr><td class="oid">#{{ $order->order_no }}</td><td>{{ $order->created_at->format('d M Y') }}</td><td class="tnum">{{ taka($order->total) }}</td><td><span class="pillst {{ $pill[$order->status] ?? 'pend' }}">{{ ucfirst($order->status) }}</span></td></tr>
                        @empty
                            <tr><td colspan="4" class="sec-sub" style="padding:18px 0">No orders yet. <a href="{{ route('products.index') }}">Start shopping</a>.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Orders --}}
            <div class="main-card" x-show="tab==='orders'" style="display:none">
                <h2>My Orders</h2>
                <table class="otable">
                    <thead><tr><th>Order</th><th>Date</th><th>Items</th><th>Total</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse ($customer->orders as $order)
                            <tr><td class="oid">#{{ $order->order_no }}</td><td>{{ $order->created_at->format('d M Y') }}</td><td>{{ $order->items()->count() }} item(s)</td><td class="tnum">{{ taka($order->total) }}</td><td><span class="pillst {{ $pill[$order->status] ?? 'pend' }}">{{ ucfirst($order->status) }}</span></td></tr>
                        @empty
                            <tr><td colspan="5" class="sec-sub" style="padding:18px 0">No orders yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Addresses --}}
            <div class="main-card" x-show="tab==='addr'" style="display:none">
                <h2>Saved Addresses</h2>
                <div class="addrs">
                    @forelse ($customer->addresses as $addr)
                        <div class="addr {{ $addr->is_default ? 'default' : '' }}">
                            @if ($addr->is_default)<span class="tag">Default</span>@endif
                            <h4>{{ $addr->label ?: 'Address' }}</h4>
                            <p>{{ $customer->name }}<br>{{ $addr->address_line }}<br>{{ $addr->city }}</p>
                        </div>
                    @empty
                        <p class="sec-sub">No saved addresses yet. Your delivery address is saved automatically on your first order.</p>
                    @endforelse
                </div>
            </div>

            {{-- Profile --}}
            <div class="main-card" x-show="tab==='profile'" style="display:none">
                <h2>Profile Details</h2>
                <form method="POST" action="{{ route('account.update') }}">
                    @csrf @method('PUT')
                    <div class="grid2">
                        <div class="field"><label>Full Name</label><input type="text" name="name" value="{{ old('name', $customer->name) }}" required></div>
                        <div class="field"><label>Mobile Number</label><input type="tel" name="phone" value="{{ old('phone', $customer->phone) }}"></div>
                    </div>
                    <div class="field"><label>Email Address</label><input type="email" value="{{ $customer->email }}" disabled></div>
                    <div class="field"><label>City</label><input type="text" name="location_city" value="{{ old('location_city', $customer->location_city) }}"></div>
                    <div style="margin-top:8px"><button class="btn btn-primary" type="submit">Save Changes</button></div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
