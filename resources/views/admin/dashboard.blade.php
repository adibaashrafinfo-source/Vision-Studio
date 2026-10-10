@extends('layouts.admin')

@section('title', 'Dashboard')

@php
    $pillClass = [
        'delivered' => 'done', 'processing' => 'proc', 'shipped' => 'ship',
        'pending' => 'pend', 'cancelled' => 'cancel',
    ];
@endphp

@section('content')
    <div class="ph-head">
        <div><h1>Dashboard</h1><p>Welcome back! Here's what's happening with your store today.</p></div>
        <div class="ph-tools"><select class="sel"><option>All Time</option></select></div>
    </div>

    <div class="qrow">
        <a class="qbtn" href="{{ route('admin.products.create') }}"><span class="ic"><svg class="icon" viewBox="0 0 24 24"><path d="M12 5v14"></path><path d="M5 12h14"></path></svg></span><div><b>Add Product</b><span>List a new item</span></div></a>
        <a class="qbtn" href="{{ route('admin.orders.index') }}"><span class="ic"><svg class="icon" viewBox="0 0 24 24"><path d="M6 2h9l5 5v15H6z"></path><path d="M14 2v6h6"></path></svg></span><div><b>View Orders</b><span>{{ number_format($totalOrders) }} total orders</span></div></a>
        <a class="qbtn" href="{{ route('admin.reports.index') }}"><span class="ic"><svg class="icon" viewBox="0 0 24 24"><path d="M4 19V9"></path><path d="M11 19V5"></path><path d="M18 19v-7"></path></svg></span><div><b>View Reports</b><span>Sales &amp; analytics</span></div></a>
        <a class="qbtn" href="{{ route('admin.website.edit') }}"><span class="ic"><svg class="icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"></circle><path d="M3 12h18"></path></svg></span><div><b>Edit Website</b><span>Hero, footer &amp; more</span></div></a>
    </div>

    <div class="stat-row s4">
        <div class="stat"><div class="top"><span class="ic g"><svg class="icon" viewBox="0 0 24 24"><path d="M3 12l5-6 4 4 9-9"></path><path d="M15 2h6v6"></path></svg></span><div><div class="v tnum">{{ $stats['sales']['value'] }}</div><div class="k">Total Sales</div></div></div>
            @if (! is_null($stats['sales']['change']))<div class="chg {{ $stats['sales']['change'] >= 0 ? 'up' : 'dn' }}">{{ $stats['sales']['change'] >= 0 ? '▲' : '▼' }} {{ abs($stats['sales']['change']) }}% vs last month</div>@endif
        </div>
        <div class="stat"><div class="top"><span class="ic b"><svg class="icon" viewBox="0 0 24 24"><path d="M6 2h9l5 5v15H6z"></path><path d="M14 2v6h6"></path></svg></span><div><div class="v tnum">{{ $stats['orders']['value'] }}</div><div class="k">Total Orders</div></div></div>
            @if (! is_null($stats['orders']['change']))<div class="chg {{ $stats['orders']['change'] >= 0 ? 'up' : 'dn' }}">{{ $stats['orders']['change'] >= 0 ? '▲' : '▼' }} {{ abs($stats['orders']['change']) }}% vs last month</div>@endif
        </div>
        <div class="stat"><div class="top"><span class="ic a"><svg class="icon" viewBox="0 0 24 24"><circle cx="9" cy="8" r="3.5"></circle><path d="M2 20c0-3.5 3-6 7-6s7 2.5 7 6"></path></svg></span><div><div class="v tnum">{{ $stats['customers']['value'] }}</div><div class="k">Total Customers</div></div></div>
            @if (! is_null($stats['customers']['change']))<div class="chg {{ $stats['customers']['change'] >= 0 ? 'up' : 'dn' }}">{{ $stats['customers']['change'] >= 0 ? '▲' : '▼' }} {{ abs($stats['customers']['change']) }}% vs last month</div>@endif
        </div>
        <div class="stat"><div class="top"><span class="ic p"><svg class="icon" viewBox="0 0 24 24"><path d="M3 7l9-4 9 4-9 4-9-4z"></path><path d="M3 7v10l9 4 9-4V7"></path></svg></span><div><div class="v tnum">{{ $stats['products']['value'] }}</div><div class="k">Total Products</div></div></div></div>
    </div>

    <div class="grid2c">
        <div class="card">
            <h3>Sales Overview</h3><div class="sub tnum">{{ $salesThisPeriod }} total</div>
            <div class="chart-box">
                <svg class="lchart" viewBox="0 0 560 180" preserveAspectRatio="none">
                    <polyline points="{{ $chartPoints }}" fill="none" stroke="#2F6FD6" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"></polyline>
                    <polygon points="{{ $chartPoints }} 560,180 0,180" fill="url(#g1)" opacity=".3"></polygon>
                    <defs><linearGradient id="g1" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#2F6FD6"></stop><stop offset="100%" stop-color="#2F6FD6" stop-opacity="0"></stop></linearGradient></defs>
                </svg>
            </div>
            <div style="display:flex;justify-content:space-between;color:var(--muted);font-size:11px;font-weight:600;margin-top:6px">
                @foreach ($monthly as $m)<span>{{ $m['label'] }}</span>@endforeach
            </div>
        </div>
        <div class="card">
            <h3>Order Status</h3><div class="sub">{{ number_format($totalOrders) }} total orders</div>
            <div class="donut-wrap" style="margin-top:16px">
                <div class="donut" style="background:conic-gradient({{ $donutGradient }})"><div class="ctr"><b>{{ number_format($totalOrders) }}</b><span>Orders</span></div></div>
                <div class="legend">
                    @foreach ($donut as $s)
                        <div class="r"><span class="sw" style="background:{{ $s['var'] }}"></span><span class="n">{{ $s['label'] }}</span><span class="val">{{ $s['count'] }}</span></div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="grid2c">
        <div class="card">
            <h3>Recent Orders</h3>
            <table class="tbl" style="margin-top:12px"><thead><tr><th>#</th><th>Customer</th><th>Amount</th><th>Status</th></tr></thead><tbody>
                @forelse ($recentOrders as $order)
                    <tr>
                        <td class="oid"><a href="{{ route('admin.orders.show', $order) }}">#{{ $order->order_no }}</a></td>
                        <td>{{ $order->customer?->name ?? 'Guest' }}</td>
                        <td class="tnum">{{ taka($order->total) }}</td>
                        <td><span class="pillst {{ $pillClass[$order->status] ?? 'pend' }}">{{ ucfirst($order->status) }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="color:var(--muted);padding:18px 12px">No orders yet.</td></tr>
                @endforelse
            </tbody></table>
        </div>
        <div class="card">
            <h3>Top Selling Products</h3>
            <table class="tbl" style="margin-top:12px"><tbody>
                @forelse ($topProducts as $item)
                    <tr>
                        <td><div class="prow">
                            <div class="pthumb">
                                @if ($item->product?->primaryImage)
                                    <img class="pimg" src="{{ asset('storage/'.$item->product->primaryImage->image_path) }}" alt="">
                                @else
                                    <svg class="icon" viewBox="0 0 24 24" style="stroke:var(--muted)"><path d="M3 7l9-4 9 4-9 4-9-4z"></path><path d="M3 7v10l9 4 9-4V7"></path></svg>
                                @endif
                            </div>
                            <div><div class="pname">{{ $item->product?->name ?? 'Deleted product' }}</div><div class="pcat">{{ $item->sold }} sales</div></div>
                        </div></td>
                        <td class="tnum" style="text-align:right;font-weight:700">{{ taka($item->revenue) }}</td>
                    </tr>
                @empty
                    <tr><td style="color:var(--muted);padding:18px 12px">No sales yet.</td></tr>
                @endforelse
            </tbody></table>
        </div>
    </div>
@endsection
