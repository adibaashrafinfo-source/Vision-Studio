<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $monthStart = now()->startOfMonth();
        $lastMonthStart = now()->subMonthNoOverflow()->startOfMonth();
        $lastMonthEnd = now()->subMonthNoOverflow()->endOfMonth();

        $paidScope = fn ($q) => $q->where('status', '!=', 'cancelled');

        // Stat cards.
        $totalSales = Order::where($paidScope)->sum('total');
        $salesThisMonth = Order::where($paidScope)->where('created_at', '>=', $monthStart)->sum('total');
        $salesLastMonth = Order::where($paidScope)->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->sum('total');

        $totalOrders = Order::count();
        $ordersThisMonth = Order::where('created_at', '>=', $monthStart)->count();
        $ordersLastMonth = Order::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();

        $totalCustomers = Customer::count();
        $customersThisMonth = Customer::where('created_at', '>=', $monthStart)->count();
        $customersLastMonth = Customer::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();

        $totalProducts = Product::count();

        // Order-status breakdown for the donut.
        $statusCounts = Order::query()
            ->selectRaw('status, COUNT(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status');

        $statuses = [
            'pending' => ['label' => 'Pending', 'var' => 'var(--amber)'],
            'processing' => ['label' => 'Processing', 'var' => 'var(--blue)'],
            'shipped' => ['label' => 'Shipped', 'var' => 'var(--blue-bright)'],
            'delivered' => ['label' => 'Delivered', 'var' => 'var(--green)'],
            'cancelled' => ['label' => 'Cancelled', 'var' => 'var(--sale)'],
        ];
        $donut = [];
        $acc = 0;
        $donutTotal = max($totalOrders, 1);
        foreach ($statuses as $key => $meta) {
            $count = (int) ($statusCounts[$key] ?? 0);
            $from = round($acc / $donutTotal * 100, 2);
            $acc += $count;
            $to = round($acc / $donutTotal * 100, 2);
            $donut[] = $meta + ['count' => $count, 'from' => $from, 'to' => $to];
        }
        $donutGradient = collect($donut)
            ->map(fn ($s) => "{$s['var']} {$s['from']}% {$s['to']}%")
            ->implode(', ');

        // Monthly sales (last 12 months) for the line chart.
        $monthly = collect(range(11, 0))->map(function ($i) use ($paidScope) {
            $start = now()->subMonthsNoOverflow($i)->startOfMonth();
            $end = (clone $start)->endOfMonth();

            return [
                'label' => $start->format('M'),
                'total' => (float) Order::where($paidScope)->whereBetween('created_at', [$start, $end])->sum('total'),
            ];
        });
        $maxMonthly = max($monthly->max('total'), 1);
        $chartPoints = $monthly->values()->map(function ($m, $idx) use ($monthly, $maxMonthly) {
            $x = $monthly->count() > 1 ? round($idx / ($monthly->count() - 1) * 560, 1) : 0;
            $y = round(170 - ($m['total'] / $maxMonthly * 150), 1);

            return "{$x},{$y}";
        })->implode(' ');

        // Recent orders + top sellers.
        $recentOrders = Order::with('customer')->latest()->limit(4)->get();

        $topProducts = OrderItem::query()
            ->selectRaw('product_id, SUM(qty) as sold, SUM(subtotal) as revenue')
            ->whereNotNull('product_id')
            ->groupBy('product_id')
            ->orderByDesc('sold')
            ->with('product.primaryImage')
            ->limit(3)
            ->get();

        return view('admin.dashboard', [
            'stats' => [
                'sales' => ['value' => taka($totalSales), 'change' => percent_change($salesThisMonth, $salesLastMonth)],
                'orders' => ['value' => number_format($totalOrders), 'change' => percent_change($ordersThisMonth, $ordersLastMonth)],
                'customers' => ['value' => number_format($totalCustomers), 'change' => percent_change($customersThisMonth, $customersLastMonth)],
                'products' => ['value' => number_format($totalProducts), 'change' => null],
            ],
            'totalOrders' => $totalOrders,
            'donut' => $donut,
            'donutGradient' => $donutGradient ?: 'var(--surface-tint) 0 100%',
            'salesThisPeriod' => taka($totalSales),
            'monthly' => $monthly,
            'chartPoints' => $chartPoints,
            'recentOrders' => $recentOrders,
            'topProducts' => $topProducts,
        ]);
    }
}
