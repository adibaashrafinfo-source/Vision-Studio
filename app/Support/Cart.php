<?php

namespace App\Support;

use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Support\Facades\Session;

/**
 * Simple session-backed shopping cart. Keyed by product id.
 * Each line: ['id','name','slug','price','qty','image'].
 */
class Cart
{
    protected const KEY = 'cart';

    public static function items(): array
    {
        return Session::get(self::KEY, []);
    }

    public static function add(Product $product, int $qty = 1): void
    {
        $items = self::items();
        $id = $product->id;

        if (isset($items[$id])) {
            $items[$id]['qty'] += $qty;
        } else {
            $items[$id] = [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => (float) $product->price,
                'qty' => $qty,
                'image' => $product->primaryImage?->image_path,
            ];
        }

        $items[$id]['qty'] = max(1, $items[$id]['qty']);
        Session::put(self::KEY, $items);
    }

    public static function update(int $id, int $qty): void
    {
        $items = self::items();
        if (! isset($items[$id])) {
            return;
        }
        if ($qty <= 0) {
            unset($items[$id]);
        } else {
            $items[$id]['qty'] = $qty;
        }
        Session::put(self::KEY, $items);
    }

    public static function remove(int $id): void
    {
        $items = self::items();
        unset($items[$id]);
        Session::put(self::KEY, $items);
    }

    public static function clear(): void
    {
        Session::forget(self::KEY);
        Session::forget('coupon');
    }

    public static function count(): int
    {
        return array_sum(array_map(fn ($i) => $i['qty'], self::items()));
    }

    public static function subtotal(): float
    {
        return array_sum(array_map(fn ($i) => $i['price'] * $i['qty'], self::items()));
    }

    public static function couponCode(): ?string
    {
        return Session::get('coupon');
    }

    /** The applied, still-valid coupon model, or null. */
    public static function coupon(): ?Coupon
    {
        $code = self::couponCode();
        if (! $code) {
            return null;
        }

        $coupon = Coupon::where('code', $code)->where('status', 'active')->first();

        if (! $coupon
            || ($coupon->starts_at && $coupon->starts_at->isFuture())
            || ($coupon->ends_at && $coupon->ends_at->isPast())
            || ($coupon->min_order && self::subtotal() < (float) $coupon->min_order)) {
            return null;
        }

        return $coupon;
    }

    public static function discount(): float
    {
        $coupon = self::coupon();
        if (! $coupon) {
            return 0;
        }

        $subtotal = self::subtotal();
        $value = $coupon->type === 'percent'
            ? $subtotal * ((float) $coupon->value / 100)
            : (float) $coupon->value;

        if ($coupon->max_discount) {
            $value = min($value, (float) $coupon->max_discount);
        }

        return round(min($value, $subtotal), 2);
    }
}
