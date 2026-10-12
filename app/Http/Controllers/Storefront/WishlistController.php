<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Support\Wishlist;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function index(): View
    {
        $products = Product::active()->with('primaryImage', 'category')
            ->withCount('approvedReviews')->withAvg('approvedReviews', 'rating')
            ->whereIn('id', Wishlist::ids())->get();

        return view('storefront.wishlist', compact('products'));
    }

    public function toggle(Product $product): RedirectResponse
    {
        $added = Wishlist::toggle($product->id);

        return back()->with('status', $added ? 'Added to wishlist.' : 'Removed from wishlist.');
    }

    public function remove(Product $product): RedirectResponse
    {
        Wishlist::remove($product->id);

        return back()->with('status', 'Removed from wishlist.');
    }
}
