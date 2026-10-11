<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $coupons = [
            ['code' => 'WELCOME10', 'title' => 'Welcome Discount', 'description' => '10% off your first order.', 'type' => 'percent', 'value' => 10, 'min_order' => 1000, 'max_discount' => 500, 'usage_limit' => 500, 'used_count' => 86, 'status' => 'active'],
            ['code' => 'EID500', 'title' => 'Eid Special', 'description' => 'Flat ৳500 off orders over ৳3,000.', 'type' => 'fixed', 'value' => 500, 'min_order' => 3000, 'max_discount' => null, 'usage_limit' => 200, 'used_count' => 54, 'status' => 'active'],
            ['code' => 'SUMMER15', 'title' => 'Summer Sunglasses Sale', 'description' => '15% off all sunglasses.', 'type' => 'percent', 'value' => 15, 'min_order' => 2000, 'max_discount' => 1500, 'usage_limit' => 150, 'used_count' => 112, 'status' => 'active', 'ends_at' => Carbon::now()->addMonths(2)],
            ['code' => 'VSOLD20', 'title' => 'Old Promo (Expired)', 'description' => 'Expired launch promo, kept for history.', 'type' => 'percent', 'value' => 20, 'min_order' => null, 'max_discount' => null, 'usage_limit' => 100, 'used_count' => 100, 'status' => 'inactive', 'ends_at' => Carbon::now()->subMonths(3)],
        ];

        foreach ($coupons as $coupon) {
            Coupon::updateOrCreate(['code' => $coupon['code']], $coupon);
        }
    }
}
