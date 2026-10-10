<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\View\View;

class ReportsController extends Controller
{
    public function index(): View
    {
        $paidScope = fn ($q) => $q->where('status', '!=', 'cancelled');

        $revenue = Order::where($paidScope)->sum('total');
        $orders = Order::count();
        $customers = Customer::count();
        $aov = $orders > 0 ? $revenue / max(Order::where($paidScope)->count(), 1) : 0;

        // Last 12 months revenue + order count for the grouped bar chart.
        $monthly = collect(range(11, 0))->map(function ($i) use ($paidScope) {
            $start = now()->subMonthsNoOverflow($i)->startOfMonth();
            $end = (clone $start)->endOfMonth();

            return [
                'label' => $start->format('M'),
                'revenue' => (float) Order::where($paidScope)->whereBetween('created_at', [$start, $end])->sum('total'),
                'orders' => Order::whereBetween('created_at', [$start, $end])->count(),
            ];
        });
        $maxRevenue = max($monthly->max('revenue'), 1);
        $maxOrders = max($monthly->max('orders'), 1);

        // Top products by revenue.
        $topProducts = OrderItem::query()
            ->selectRaw('product_id, SUM(qty) as sold, SUM(subtotal) as revenue')
            ->whereNotNull('product_id')
            ->groupBy('product_id')
            ->orderByDesc('revenue')
            ->with('product')
            ->limit(6)
            ->get();

        // Revenue by category.
        $byCategory = OrderItem::query()
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->selectRaw('categories.name as name, SUM(order_items.subtotal) as revenue')
            ->groupBy('categories.name')
            ->orderByDesc('revenue')
            ->limit(6)
            ->get();
        $categoryTotal = max($byCategory->sum('revenue'), 1);

        // Order status split.
        $statusSplit = Order::selectRaw('status, COUNT(*) as c')->groupBy('status')->pluck('c', 'status');

        return view('admin.reports', [
            'kpis' => [
                'revenue' => taka($revenue),
                'orders' => number_format($orders),
                'customers' => number_format($customers),
                'aov' => taka($aov),
            ],
            'monthly' => $monthly,
            'maxRevenue' => $maxRevenue,
            'maxOrders' => $maxOrders,
            'topProducts' => $topProducts,
            'byCategory' => $byCategory,
            'categoryTotal' => $categoryTotal,
            'statusSplit' => $statusSplit,
        ]);
    }
}
