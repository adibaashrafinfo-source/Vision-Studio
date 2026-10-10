<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrdersController extends Controller
{
    public const STATUSES = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

    public function index(Request $request): View
    {
        $query = Order::query()->with('customer')->withCount('items');

        if ($search = $request->string('q')->trim()->value()) {
            $query->where('order_no', 'like', "%{$search}%")
                ->orWhereHas('customer', fn ($q) => $q->where('name', 'like', "%{$search}%"));
        }
        if (in_array($request->get('status'), self::STATUSES, true)) {
            $query->where('status', $request->get('status'));
        }

        $orders = $query->latest()->paginate(12)->withQueryString();

        $byStatus = Order::selectRaw('status, COUNT(*) as c')->groupBy('status')->pluck('c', 'status');
        $stats = [
            'total' => Order::count(),
            'pending' => (int) ($byStatus['pending'] ?? 0),
            'processing' => (int) ($byStatus['processing'] ?? 0),
            'shipped' => (int) ($byStatus['shipped'] ?? 0),
            'delivered' => (int) ($byStatus['delivered'] ?? 0),
            'cancelled' => (int) ($byStatus['cancelled'] ?? 0),
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    public function show(Order $order): View
    {
        $order->load('items.product', 'customer.addresses');

        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', self::STATUSES)],
            'payment_status' => ['nullable', 'in:unpaid,paid,refunded'],
        ]);

        $order->update($data);

        return back()->with('status', "Order #{$order->order_no} updated.");
    }
}
