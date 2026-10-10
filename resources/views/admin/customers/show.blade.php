@extends('layouts.admin')

@section('title', $customer->name)

@php
    $init = collect(explode(' ', $customer->name))->take(2)->map(fn($w)=>mb_strtoupper(mb_substr($w,0,1)))->implode('');
    $pillClass = ['delivered'=>'done','processing'=>'proc','shipped'=>'ship','pending'=>'pend','cancelled'=>'cancel'];
@endphp

@section('content')
    <div class="ph-head">
        <div><h1>{{ $customer->name }}</h1><p>Customer since {{ $customer->created_at->format('M d, Y') }}</p></div>
        <div class="ph-tools"><a class="btn btn-ghost" href="{{ route('admin.customers.index') }}">← Back to Customers</a></div>
    </div>

    <div class="grid2c">
        <div class="card">
            <h3>Profile</h3>
            <div class="cust" style="margin:12px 0"><div class="cav" style="width:48px;height:48px;font-size:15px">{{ $init }}</div><div><div class="pname">{{ $customer->name }}</div><div class="pcat">{{ ucfirst($customer->customer_group ?? 'regular') }} · <span class="pillst {{ $customer->status==='active'?'done':'cancel' }}">{{ ucfirst($customer->status) }}</span></div></div></div>
            <div class="info-row"><svg class="icon" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="M3 7l9 6 9-6"></path></svg>{{ $customer->email }}</div>
            @if ($customer->phone)<div class="info-row"><svg class="icon" viewBox="0 0 24 24"><path d="M4 4h4l2 5-3 2a14 14 0 0 0 6 6l2-3 5 2v4a2 2 0 0 1-2 2A17 17 0 0 1 2 6a2 2 0 0 1 2-2z"></path></svg>{{ $customer->phone }}</div>@endif
            @if ($customer->location_city)<div class="info-row"><svg class="icon" viewBox="0 0 24 24"><path d="M12 21s-7-6-7-11a7 7 0 0 1 14 0c0 5-7 11-7 11z"></path><circle cx="12" cy="10" r="2.5"></circle></svg>{{ $customer->location_city }}</div>@endif
            @foreach ($customer->addresses as $addr)
                <div class="info-row"><svg class="icon" viewBox="0 0 24 24"><path d="M3 9l9-6 9 6v11H3z"></path></svg>{{ $addr->label ? $addr->label.': ' : '' }}{{ $addr->address_line }}{{ $addr->city ? ', '.$addr->city : '' }}</div>
            @endforeach
            @if ($customer->notes)<h4 style="font-size:13px;margin:14px 0 6px">Notes</h4><p class="sec-sub">{{ $customer->notes }}</p>@endif
        </div>

        <div class="card">
            <h3>Order History</h3>
            <table class="tbl" style="margin-top:12px"><thead><tr><th>#</th><th>Date</th><th>Amount</th><th>Status</th></tr></thead><tbody>
                @forelse ($customer->orders()->latest()->get() as $order)
                    <tr>
                        <td class="oid"><a href="{{ route('admin.orders.show', $order) }}">#{{ $order->order_no }}</a></td>
                        <td class="pcat">{{ $order->created_at->format('M d, Y') }}</td>
                        <td class="tnum">{{ taka($order->total) }}</td>
                        <td><span class="pillst {{ $pillClass[$order->status] ?? 'pend' }}">{{ ucfirst($order->status) }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="color:var(--muted);padding:18px 12px">No orders yet.</td></tr>
                @endforelse
            </tbody></table>
        </div>
    </div>
@endsection
