<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $categories = Category::active()->whereNull('parent_id')->orderBy('position')->limit(6)->get();

        $active = Product::active()->with('primaryImage', 'category')
            ->withCount('approvedReviews')->withAvg('approvedReviews', 'rating');

        // Flash sale = active products with a compare_price (on sale).
        $flash = (clone $active)->whereNotNull('compare_price')
            ->whereColumn('compare_price', '>', 'price')->latest()->limit(4)->get();

        // Best sellers = top by units sold, falling back to featured/newest.
        $topIds = OrderItem::selectRaw('product_id, SUM(qty) as sold')
            ->whereNotNull('product_id')->groupBy('product_id')
            ->orderByDesc('sold')->limit(6)->pluck('product_id')->all();

        $bestSellers = (clone $active)
            ->when($topIds, fn ($q) => $q->whereIn('id', $topIds))
            ->when(! $topIds, fn ($q) => $q->where('featured', true))
            ->get();
        if ($bestSellers->count() < 6) {
            $bestSellers = (clone $active)->latest()->limit(6)->get();
        }

        $products = (clone $active)->latest()->limit(6)->get();

        $brands = Product::active()->whereNotNull('brand')->distinct()->orderBy('brand')->pluck('brand')->take(8);

        return view('storefront.home', compact('categories', 'flash', 'bestSellers', 'products', 'brands'));
    }
}
