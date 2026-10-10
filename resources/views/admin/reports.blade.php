@extends('layouts.admin')

@section('title', 'Reports')

@php
    $statusMeta = ['pending'=>['Pending','var(--amber)'],'processing'=>['Processing','var(--blue)'],'shipped'=>['Shipped','var(--blue-bright)'],'delivered'=>['Delivered','var(--green)'],'cancelled'=>['Cancelled','var(--sale)']];
    $statusTotal = max($statusSplit->sum(), 1);
@endphp

@section('content')
    <div class="ph-head">
        <div><h1>Reports</h1><p>Sales, orders and customer analytics across your store.</p></div>
        <div class="ph-tools"><select class="sel"><option>All Time</option></select></div>
    </div>

    <div class="stat-row s4">
        <div class="stat"><div class="top"><span class="ic g"><svg class="icon" viewBox="0 0 24 24"><path d="M3 12l5-6 4 4 9-9"></path><path d="M15 2h6v6"></path></svg></span><div><div class="v tnum">{{ $kpis['revenue'] }}</div><div class="k">Total Sales</div></div></div></div>
        <div class="stat"><div class="top"><span class="ic b"><svg class="icon" viewBox="0 0 24 24"><path d="M6 2h9l5 5v15H6z"></path><path d="M14 2v6h6"></path></svg></span><div><div class="v tnum">{{ $kpis['orders'] }}</div><div class="k">Total Orders</div></div></div></div>
        <div class="stat"><div class="top"><span class="ic a"><svg class="icon" viewBox="0 0 24 24"><circle cx="9" cy="8" r="3.5"></circle><path d="M2 20c0-3.5 3-6 7-6s7 2.5 7 6"></path></svg></span><div><div class="v tnum">{{ $kpis['customers'] }}</div><div class="k">Total Customers</div></div></div></div>
        <div class="stat"><div class="top"><span class="ic p"><svg class="icon" viewBox="0 0 24 24"><path d="M12 1v22"></path><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg></span><div><div class="v tnum">{{ $kpis['aov'] }}</div><div class="k">Avg. Order Value</div></div></div></div>
    </div>

    <div class="grid2c">
        <div class="card">
            <h3>Sales Overview</h3><div class="sub">Revenue over the last 12 months</div>
            <div class="bars">
                @foreach ($monthly as $m)
                    <div class="bc"><div class="b" style="height:{{ max(3, round($m['revenue'] / $maxRevenue * 100)) }}%"></div><div class="lb">{{ $m['label'] }}</div></div>
                @endforeach
            </div>
        </div>
        <div class="card">
            <h3>Order Status</h3><div class="sub">{{ number_format($statusTotal) }} total orders</div>
            <div class="legend" style="margin-top:16px">
                @foreach ($statusMeta as $key => [$label, $color])
                    @php $c = (int)($statusSplit[$key] ?? 0); @endphp
                    <div class="r"><span class="sw" style="background:{{ $color }}"></span><span class="n">{{ $label }}</span><span class="val">{{ $c }}</span></div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid2c">
        <div class="card">
            <h3>Top Selling Products</h3>
            <table class="tbl" style="margin-top:12px"><thead><tr><th>Product</th><th>Units</th><th>Revenue</th></tr></thead><tbody>
                @forelse ($topProducts as $item)
                    <tr><td class="pname">{{ $item->product?->name ?? 'Deleted' }}</td><td class="tnum">{{ $item->sold }}</td><td class="tnum" style="font-weight:700">{{ taka($item->revenue) }}</td></tr>
                @empty
                    <tr><td colspan="3" style="color:var(--muted);padding:18px 12px">No sales yet.</td></tr>
                @endforelse
            </tbody></table>
        </div>
        <div class="card">
            <h3>Revenue by Category</h3>
            <div style="margin-top:14px;display:flex;flex-direction:column;gap:14px">
                @forelse ($byCategory as $row)
                    <div>
                        <div style="display:flex;justify-content:space-between;font-size:12.5px;margin-bottom:5px"><span class="n" style="font-weight:600">{{ $row->name }}</span><span class="tnum" style="font-weight:700">{{ taka($row->revenue) }}</span></div>
                        <div style="height:9px;border-radius:999px;background:var(--surface-tint);overflow:hidden"><div style="height:100%;width:{{ round($row->revenue / $categoryTotal * 100) }}%;background:linear-gradient(90deg,var(--blue),var(--blue-bright))"></div></div>
                    </div>
                @empty
                    <p class="sec-sub">No category sales yet.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
