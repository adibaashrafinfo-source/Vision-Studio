<?php

namespace App\Support;

use Illuminate\Support\Facades\Session;

/**
 * Session-backed wishlist storing a flat list of product ids. Same lifetime
 * as the user's browser session — intentionally lightweight, same pattern
 * as the cart.
 */
class Wishlist
{
    protected const KEY = 'wishlist';

    /** @return int[] */
    public static function ids(): array
    {
        return array_values(array_unique(Session::get(self::KEY, [])));
    }

    public static function has(int $productId): bool
    {
        return in_array($productId, self::ids(), true);
    }

    public static function toggle(int $productId): bool
    {
        $ids = self::ids();
        $index = array_search($productId, $ids, true);

        if ($index === false) {
            $ids[] = $productId;
            Session::put(self::KEY, $ids);

            return true;
        }

        unset($ids[$index]);
        Session::put(self::KEY, array_values($ids));

        return false;
    }

    public static function remove(int $productId): void
    {
        Session::put(self::KEY, array_values(array_filter(self::ids(), fn ($id) => $id !== $productId)));
    }

    public static function count(): int
    {
        return count(self::ids());
    }

    public static function clear(): void
    {
        Session::forget(self::KEY);
    }
}
