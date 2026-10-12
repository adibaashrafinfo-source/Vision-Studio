<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Order #{{ $order->order_no }}</title>
</head>
<body style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#f5f7fa;margin:0;padding:0;color:#1a1f2e">
<div style="max-width:600px;margin:0 auto;background:#fff;padding:36px 32px;border-radius:12px;margin-top:24px;margin-bottom:24px">
    <h1 style="color:#0A1A42;margin:0 0 16px;font-size:24px">Thank you for your order!</h1>
    <p>Hi {{ $order->customer->name ?? 'there' }},</p>
    <p>We've received your order <strong style="color:#1E6BFF">#{{ $order->order_no }}</strong> and it's now being processed. You'll hear from us again when it ships.</p>

    <table style="width:100%;border-collapse:collapse;margin:24px 0;border:1px solid #e5e7eb;border-radius:8px;overflow:hidden">
        <thead>
            <tr style="background:#f5f7fa;text-align:left">
                <th style="padding:12px;font-size:13px;color:#6b7280;font-weight:600">Item</th>
                <th style="padding:12px;font-size:13px;color:#6b7280;font-weight:600;text-align:right">Qty</th>
                <th style="padding:12px;font-size:13px;color:#6b7280;font-weight:600;text-align:right">Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr style="border-top:1px solid #e5e7eb">
                    <td style="padding:12px">{{ $item->product_name }}</td>
                    <td style="padding:12px;text-align:right">{{ $item->qty }}</td>
                    <td style="padding:12px;text-align:right">{{ taka($item->subtotal) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table style="width:100%;margin:0 0 24px">
        <tr><td style="padding:4px 0;color:#6b7280">Subtotal</td><td style="padding:4px 0;text-align:right">{{ taka($order->subtotal) }}</td></tr>
        @if ($order->discount > 0)<tr><td style="padding:4px 0;color:#dc2626">Discount</td><td style="padding:4px 0;text-align:right;color:#dc2626">−{{ taka($order->discount) }}</td></tr>@endif
        <tr><td style="padding:4px 0;color:#6b7280">Shipping</td><td style="padding:4px 0;text-align:right">{{ $order->shipping_fee == 0 ? 'Free' : taka($order->shipping_fee) }}</td></tr>
        <tr style="font-weight:700;font-size:18px"><td style="padding:12px 0 0;border-top:2px solid #0A1A42">Total</td><td style="padding:12px 0 0;text-align:right;border-top:2px solid #0A1A42">{{ taka($order->total) }}</td></tr>
    </table>

    <p style="font-size:14px;color:#6b7280">Payment: <strong style="color:#1a1f2e">{{ strtoupper($order->payment_method) }}</strong> · {{ ucfirst($order->payment_status) }}</p>

    @if ($isNewAccount ?? false)
        <div style="background:#f0f7ff;border:1px solid #cfe3ff;border-radius:8px;padding:16px;margin:24px 0;font-size:14px">
            <strong>We created an account for you</strong><br>
            Email: {{ $order->customer->email }}<br>
            To access your order history, use <a href="{{ route('customer.password.request') }}" style="color:#1E6BFF">Forgot Password</a> on our site to set a password for your account.
        </div>
    @endif

    <p style="margin-top:32px;font-size:13px;color:#6b7280">
        Questions? Reply to this email or visit <a href="{{ url('/') }}" style="color:#1E6BFF">{{ config('app.name') }}</a>.
    </p>
</div>
</body>
</html>
