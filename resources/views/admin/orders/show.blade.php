@extends('layouts.admin')

@section('title', 'Order #'.$order->order_no)

@php $pillClass = ['delivered'=>'done','processing'=>'proc','shipped'=>'ship','pending'=>'pend','cancelled'=>'cancel']; @endphp

@section('content')
    <div class="ph-head">
        <div class="detail-h">
            <h1 style="display:flex;align-items:center;gap:10px">Order #{{ $order->order_no }} <span class="pillst {{ $pillClass[$order->status] ?? 'pend' }}">{{ ucfirst($order->status) }}</span></h1>
            <p>Placed on {{ $order->created_at->format('M d, Y') }} at {{ $order->created_at->format('h:i A') }}</p>
        </div>
        <div class="ph-tools"><a class="btn btn-ghost" href="{{ route('admin.orders.index') }}">← Back to Orders</a></div>
    </div>

    <div class="grid2c">
        <div class="card">
            <h3>Order Items</h3>
            <div style="margin-top:10px">
                @foreach ($order->items as $item)
                    <div class="oitem {{ $loop->last ? '' : '' }}">
                        <div class="pthumb sm">
                            @if ($item->product?->primaryImage)<img class="pimg" src="{{ asset('storage/'.$item->product->primaryImage->image_path) }}" alt="">
                            @else<svg class="icon" viewBox="0 0 24 24" style="stroke:var(--muted)"><path d="M3 7l9-4 9 4-9 4-9-4z"></path><path d="M3 7v10l9 4 9-4V7"></path></svg>@endif
                        </div>
                        <div style="flex:1"><div class="pname" style="font-size:12.5px">{{ $item->product_name ?: $item->product?->name ?? 'Product' }}</div><div class="v">× {{ $item->qty }} @ {{ taka($item->unit_price) }}</div></div>
                        <b class="tnum" style="font-size:12.5px">{{ taka($item->subtotal) }}</b>
                    </div>
                @endforeach
            </div>
            <div style="margin-top:14px;padding-top:4px">
                <div class="ordsum"><span>Subtotal</span><b class="tnum">{{ taka($order->subtotal) }}</b></div>
                @if ($order->discount > 0)<div class="ordsum"><span>Discount</span><span class="tnum" style="color:var(--sale)">(-) {{ taka($order->discount) }}</span></div>@endif
                <div class="ordsum"><span>Shipping Charge</span><b class="tnum">{{ taka($order->shipping_fee) }}</b></div>
                <div class="ordsum tot"><span>Total Amount</span><b class="tnum">{{ taka($order->total) }}</b></div>
            </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:16px">
            <div class="card">
                <h3>Customer</h3>
                @if ($order->customer)
                    <div class="info-row"><svg class="icon" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"></circle><path d="M4 20c0-4 4-6 8-6s8 2 8 6"></path></svg>{{ $order->customer->name }}</div>
                    @if ($order->customer->phone)<div class="info-row"><svg class="icon" viewBox="0 0 24 24"><path d="M4 4h4l2 5-3 2a14 14 0 0 0 6 6l2-3 5 2v4a2 2 0 0 1-2 2A17 17 0 0 1 2 6a2 2 0 0 1 2-2z"></path></svg>{{ $order->customer->phone }}</div>@endif
                    <div class="info-row"><svg class="icon" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="M3 7l9 6 9-6"></path></svg>{{ $order->customer->email }}</div>
                    @foreach ($order->customer->addresses as $addr)
                        <div class="info-row"><svg class="icon" viewBox="0 0 24 24"><path d="M12 21s-7-6-7-11a7 7 0 0 1 14 0c0 5-7 11-7 11z"></path><circle cx="12" cy="10" r="2.5"></circle></svg>{{ $addr->address_line }}{{ $addr->city ? ', '.$addr->city : '' }}</div>
                    @endforeach
                @else
                    <p class="sec-sub">Guest order — no customer account.</p>
                @endif
            </div>

            <div class="card">
                <h3>Update Order</h3>
                <form method="POST" action="{{ route('admin.orders.update', $order) }}" style="margin-top:12px">
                    @csrf @method('PUT')
                    <div class="field"><label>Order Status</label>
                        <select name="status">
                            @foreach (\App\Http\Controllers\Admin\OrdersController::STATUSES as $st)
                                <option value="{{ $st }}" @selected($order->status===$st)>{{ ucfirst($st) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field"><label>Payment Status</label>
                        <select name="payment_status">
                            @foreach (['unpaid','paid','refunded'] as $ps)
                                <option value="{{ $ps }}" @selected($order->payment_status===$ps)>{{ ucfirst($ps) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="info-row"><svg class="icon" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="M3 10h18"></path></svg>Payment Method <b style="margin-left:auto">{{ ucfirst($order->payment_method ?: 'COD') }}</b></div>
                    <button type="submit" class="btn btn-primary" style="width:100%;margin-top:12px">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
@endsection
