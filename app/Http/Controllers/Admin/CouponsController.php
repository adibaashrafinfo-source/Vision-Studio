<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CouponsController extends Controller
{
    public function index(Request $request): View
    {
        $query = Coupon::query();

        if ($search = $request->string('q')->trim()->value()) {
            $query->where('code', 'like', "%{$search}%")->orWhere('title', 'like', "%{$search}%");
        }
        if (in_array($request->get('status'), ['active', 'inactive', 'expired'], true)) {
            $query->where('status', $request->get('status'));
        }

        $coupons = $query->latest()->paginate(12)->withQueryString();

        $stats = [
            'total' => Coupon::count(),
            'active' => Coupon::where('status', 'active')->count(),
            'used' => (int) Coupon::sum('used_count'),
            'expired' => Coupon::whereNotNull('ends_at')->where('ends_at', '<', now())->count(),
        ];

        return view('admin.coupons', compact('coupons', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        Coupon::create($this->validateData($request));

        return redirect()->route('admin.coupons.index')->with('status', 'Coupon created.');
    }

    public function update(Request $request, Coupon $coupon): RedirectResponse
    {
        $coupon->update($this->validateData($request, $coupon));

        return redirect()->route('admin.coupons.index')->with('status', 'Coupon updated.');
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        $coupon->delete();

        return redirect()->route('admin.coupons.index')->with('status', 'Coupon deleted.');
    }

    private function validateData(Request $request, ?Coupon $coupon = null): array
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:60', Rule::unique('coupons', 'code')->ignore($coupon?->id)],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:percent,fixed'],
            'value' => ['required', 'numeric', 'min:0'],
            'min_order' => ['nullable', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'status' => ['required', 'in:active,inactive'],
        ]);
        $data['code'] = strtoupper($data['code']);

        return $data;
    }
}
