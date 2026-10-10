<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        return $this->listing($request, null);
    }

    public function category(Request $request, Category $category): View
    {
        return $this->listing($request, $category);
    }

    protected function listing(Request $request, ?Category $category): View
    {
        $query = Product::active()->with('primaryImage', 'category');

        if ($category) {
            $query->where('category_id', $category->id);
        }
        if ($search = $request->string('q')->trim()->value()) {
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('brand', 'like', "%{$search}%")
                ->orWhere('short_desc', 'like', "%{$search}%"));
        }
        if ($request->filled('brand')) {
            $query->where('brand', $request->get('brand'));
        }
        if ($request->filled('min')) {
            $query->where('price', '>=', (float) $request->get('min'));
        }
        if ($request->filled('max')) {
            $query->where('price', '<=', (float) $request->get('max'));
        }

        $query->when($request->get('sort'), function ($q, $sort) {
            match ($sort) {
                'price_low' => $q->orderBy('price'),
                'price_high' => $q->orderByDesc('price'),
                'name' => $q->orderBy('name'),
                default => $q->latest(),
            };
        }, fn ($q) => $q->latest());

        $products = $query->paginate(12)->withQueryString();

        $categories = Category::active()->orderBy('position')->get();
        $brands = Product::active()->whereNotNull('brand')->distinct()->orderBy('brand')->pluck('brand');

        return view('storefront.category', compact('products', 'category', 'categories', 'brands'));
    }

    public function show(Product $product): View
    {
        abort_if($product->status !== 'active', 404);

        $product->load('images', 'variants', 'category', 'approvedReviews.customer');

        $related = Product::active()->with('primaryImage', 'category')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(4)->get();

        $avgRating = round((float) $product->approvedReviews->avg('rating'), 1);

        return view('storefront.product', compact('product', 'related', 'avgRating'));
    }
}
