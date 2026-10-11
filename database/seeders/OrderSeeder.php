<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        if (Order::exists()) {
            return;
        }

        $customers = Customer::all();
        $products = Product::where('status', 'active')->get();

        if ($customers->isEmpty() || $products->isEmpty()) {
            return;
        }

        // [customer index, [[product index, qty], ...], status, payment_method, payment_status, days ago]
        $plan = [
            [0, [[0, 1]], 'delivered', 'cod', 'paid', 18],
            [1, [[6, 1], [12, 2]], 'delivered', 'bkash', 'paid', 16],
            [2, [[3, 1]], 'delivered', 'card', 'paid', 15],
            [3, [[9, 1]], 'shipped', 'nagad', 'paid', 9],
            [4, [[0, 1], [11, 1]], 'shipped', 'cod', 'unpaid', 8],
            [5, [[7, 1]], 'processing', 'bkash', 'paid', 5],
            [6, [[12, 1], [13, 3]], 'processing', 'cod', 'unpaid', 4],
            [0, [[6, 1]], 'pending', 'card', 'unpaid', 2],
            [1, [[15, 1]], 'pending', 'cod', 'unpaid', 1],
            [7, [[5, 1], [10, 1]], 'delivered', 'bkash', 'paid', 22],
            [2, [[14, 1]], 'cancelled', 'cod', 'unpaid', 12],
            [3, [[1, 1], [16, 1]], 'delivered', 'card', 'paid', 20],
        ];

        $seq = 1000;

        foreach ($plan as [$custIdx, $lines, $status, $method, $payStatus, $daysAgo]) {
            $customer = $customers[$custIdx % $customers->count()];
            $createdAt = Carbon::now()->subDays($daysAgo);

            $subtotal = 0;
            $items = [];
            foreach ($lines as [$prodIdx, $qty]) {
                $product = $products[$prodIdx % $products->count()];
                $lineTotal = $product->price * $qty;
                $subtotal += $lineTotal;
                $items[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'qty' => $qty,
                    'unit_price' => $product->price,
                    'subtotal' => $lineTotal,
                ];
            }

            $shipping = $subtotal >= 2000 ? 0 : 60;
            $discount = $status === 'cancelled' ? 0 : round($subtotal * 0.0, 2);

            $order = Order::create([
                'order_no' => 'VS-'.(++$seq),
                'customer_id' => $customer->id,
                'status' => $status,
                'payment_method' => $method,
                'payment_status' => $payStatus,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'shipping_fee' => $shipping,
                'total' => $subtotal - $discount + $shipping,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            foreach ($items as $item) {
                $order->items()->create($item);
            }
        }
    }
}
