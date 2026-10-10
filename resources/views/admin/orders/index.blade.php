@extends('layouts.admin')

@section('title', 'Orders')

@php
    $pillClass = ['delivered'=>'done','processing'=>'proc','shipped'=>'ship','pending'=>'pend','cancelled'=>'cancel'];
    $avColors = ['', 'c2', 'c3', 'c4', 'c5'];
@endphp

@section('content')
    <div class="ph-head">
        <div><h1>Orders</h1><p>Manage and track all customer orders. View, process, and update order status.</p></div>
    </div>

    <div class="stat-row s6">
        <div class="stat"><div class="top"><span class="ic b"><svg class="icon" viewBox="0 0 24 24"><path d="M6 2h9l5 5v15H6z"></path><path d="M14 2v6h6"></path></svg></span><div><div class="v tnum">{{ $stats['total'] }}</div><div class="k">Total Orders</div></div></div></div>
        <div class="stat"><div class="top"><span class="ic a"><svg class="icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path></svg></span><div><div class="v tnum">{{ $stats['pending'] }}</div><div class="k">Pending</div></div></div></div>
        <div class="stat"><div class="top"><span class="ic b"><svg class="icon" viewBox="0 0 24 24"><path d="M3 7l9-4 9 4-9 4-9-4z"></path><path d="M3 7v10l9 4 9-4V7"></path></svg></span><div><div class="v tnum">{{ $stats['processing'] }}</div><div class="k">Processing</div></div></div></div>
        <div class="stat"><div class="top"><span class="ic g"><svg class="icon" viewBox="0 0 24 24"><path d="M3 7h11v8H3z"></path><path d="M14 10h4l3 3v2h-7z"></path><circle cx="7" cy="17" r="2"></circle><circle cx="17" cy="17" r="2"></circle></svg></span><div><div class="v tnum">{{ $stats['shipped'] }}</div><div class="k">Shipped</div></div></div></div>
        <div class="stat"><div class="top"><span class="ic p"><svg class="icon" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"></path></svg></span><div><div class="v tnum">{{ $stats['delivered'] }}</div><div class="k">Delivered</div></div></div></div>
        <div class="stat"><div class="top"><span class="ic r"><svg class="icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"></circle><path d="M15 9l-6 6"></path><path d="M9 9l6 6"></path></svg></span><div><div class="v tnum">{{ $stats['cancelled'] }}</div><div class="k">Cancelled</div></div></div></div>
    </div>

    <div class="card">
        <div class="fpills">
            <a href="{{ route('admin.orders.index') }}"><button class="{{ ! request('status') ? 'on' : '' }}">All Orders ({{ $stats['total'] }})</button></a>
            @foreach (['pending','processing','shipped','delivered','cancelled'] as $st)
                <a href="{{ route('admin.orders.index', ['status'=>$st]) }}"><button class="{{ request('status')===$st ? 'on' : '' }}">{{ ucfirst($st) }} ({{ $stats[$st] }})</button></a>
            @endforeach
        </div>
        <form class="tbar" method="GET" action="{{ route('admin.orders.index') }}">
            <div class="srch"><svg class="icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"></circle><path d="M21 21l-4-4"></path></svg><input name="q" value="{{ request('q') }}" placeholder="Search by order ID or customer name…"></div>
            @if (request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
            <button class="btn btn-ghost btn-sm">Search</button>
        </form>
        <table class="tbl"><thead><tr><th>#</th><th>Customer</th><th>Items</th><th>Amount</th><th>Payment</th><th>Status</th><th>Date</th><th></th></tr></thead>
        <tbody>
            @forelse ($orders as $order)
                @php $name = $order->customer?->name ?? 'Guest'; $init = collect(explode(' ', $name))->take(2)->map(fn($w)=>mb_strtoupper(mb_substr($w,0,1)))->implode(''); @endphp
                <tr onclick="window.location='{{ route('admin.orders.show', $order) }}'" style="cursor:pointer">
                    <td class="oid">#{{ $order->order_no }}</td>
                    <td><div class="cust"><div class="cav {{ $avColors[$order->id % 5] }}">{{ $init }}</div><div><div class="pname">{{ $name }}</div><div class="pcat">{{ $order->customer?->phone }}</div></div></div></td>
                    <td class="pcat">{{ $order->items_count }} item{{ $order->items_count === 1 ? '' : 's' }}</td>
                    <td><b class="tnum">{{ taka($order->total) }}</b></td>
                    <td><div class="paych">
                        <span class="pillst {{ $order->payment_status === 'paid' ? 'done' : 'pend' }}">{{ $order->payment_status === 'paid' ? 'Paid' : strtoupper($order->payment_method ?: 'COD') }}</span>
                        @if ($order->payment_method && $order->payment_status === 'paid')<span class="pm">{{ ucfirst($order->payment_method) }}</span>@endif
                    </div></td>
                    <td><span class="pillst {{ $pillClass[$order->status] ?? 'pend' }}">{{ ucfirst($order->status) }}</span></td>
                    <td><div style="font-size:12.5px">{{ $order->created_at->format('M d, Y') }}<div class="pcat">{{ $order->created_at->format('h:i A') }}</div></div></td>
                    <td><div class="acts"><a class="ic-btn" href="{{ route('admin.orders.show', $order) }}" onclick="event.stopPropagation()" title="View"><svg class="icon xs" viewBox="0 0 24 24"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"></path><circle cx="12" cy="12" r="3"></circle></svg></a></div></td>
                </tr>
            @empty
                <tr><td colspan="8" style="color:var(--muted);padding:22px 12px">No orders found.</td></tr>
            @endforelse
        </tbody></table>
        <div class="tbl-foot">
            <div class="pginfo">Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} orders</div>
            <div class="pg">{{ $orders->links('vendor.pagination.admin') }}</div>
        </div>
    </div>
@endsection
