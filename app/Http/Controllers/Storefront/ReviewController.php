<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['nullable', 'string', 'max:120'],
            'comment' => ['required', 'string', 'max:2000'],
        ]);

        $customer = auth('web')->user();

        $product->reviews()->create([
            'customer_id' => $customer?->id,
            'author_name' => $customer?->name,
            'rating' => $data['rating'],
            'title' => $data['title'] ?? null,
            'comment' => $data['comment'],
            'status' => 'pending',
        ]);

        return back()->with('status', 'Thanks! Your review has been submitted and will show here once approved.');
    }
}
