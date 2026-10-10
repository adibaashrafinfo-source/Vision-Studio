<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewsController extends Controller
{
    public function index(Request $request): View
    {
        $query = Review::query()->with('product', 'customer', 'images');

        if (in_array($request->get('status'), ['pending', 'approved', 'rejected'], true)) {
            $query->where('status', $request->get('status'));
        }
        if ($request->filled('rating')) {
            $query->where('rating', (int) $request->get('rating'));
        }

        $reviews = $query->latest()->paginate(10)->withQueryString();

        $stats = [
            'total' => Review::count(),
            'avg' => round((float) Review::where('status', 'approved')->avg('rating'), 1),
            'approved' => Review::where('status', 'approved')->count(),
            'pending' => Review::where('status', 'pending')->count(),
            'rejected' => Review::where('status', 'rejected')->count(),
        ];

        return view('admin.reviews', compact('reviews', 'stats'));
    }

    public function update(Request $request, Review $review): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected'],
        ]);
        $review->update($data);

        return back()->with('status', 'Review '.$data['status'].'.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return back()->with('status', 'Review deleted.');
    }
}
