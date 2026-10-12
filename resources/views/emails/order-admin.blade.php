<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>New order #{{ $order->order_no }}</title>
</head>
<body style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#f5f7fa;margin:0;padding:0;color:#1a1f2e">
<div style="max-width:600px;margin:0 auto;background:#fff;padding:36px 32px;border-radius:12px;margin-top:24px;margin-bottom:24px">
    <h1 style="color:#0A1A42;margin:0 0 16px;font-size:22px">New order placed</h1>

    <table style="width:100%;margin:16px 0">
        <tr><td style="padding:6px 0;color:#6b7280;width:140px">Order</td><td style="padding:6px 0"><strong>#{{ $order->order_no }}</strong></td></tr>
        <tr><td style="padding:6px 0;color:#6b7280">Customer</td><td style="padding:6px 0">{{ $order->customer->name ?? '—' }} ({{ $order->customer->email ?? '—' }})</td></tr>
        <tr><td style="padding:6px 0;color:#6b7280">Phone</td><td style="padding:6px 0">{{ $order->customer->phone ?? '—' }}</td></tr>
        <tr><td style="padding:6px 0;color:#6b7280">Payment</td><td style="padding:6px 0">{{ strtoupper($order->payment_method) }} · {{ ucfirst($order->payment_status) }}</td></tr>
        <tr><td style="padding:6px 0;color:#6b7280">Total</td><td style="padding:6px 0"><strong style="font-size:18px">{{ taka($order->total) }}</strong></td></tr>
    </table>

    <table style="width:100%;border-collapse:collapse;margin:16px 0;border:1px solid #e5e7eb;border-radius:8px;overflow:hidden">
        <thead><tr style="background:#f5f7fa"><th style="padding:10px;text-align:left;font-size:13px;color:#6b7280">Item</th><th style="padding:10px;text-align:right;font-size:13px;color:#6b7280">Qty</th><th style="padding:10px;text-align:right;font-size:13px;color:#6b7280">Subtotal</th></tr></thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr style="border-top:1px solid #e5e7eb"><td style="padding:10px">{{ $item->product_name }}</td><td style="padding:10px;text-align:right">{{ $item->qty }}</td><td style="padding:10px;text-align:right">{{ taka($item->subtotal) }}</td></tr>
            @endforeach
        </tbody>
    </table>

    <p style="margin-top:24px"><a href="{{ route('admin.orders.show', $order) }}" style="display:inline-block;background:#1E6BFF;color:#fff;padding:10px 18px;border-radius:8px;text-decoration:none">Open in admin panel</a></p>
</div>
</body>
</html>
