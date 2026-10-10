<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function index(): View
    {
        $customer = auth('web')->user();
        $customer->load(['orders' => fn ($q) => $q->latest(), 'addresses']);

        return view('storefront.account', compact('customer'));
    }

    public function update(Request $request): RedirectResponse
    {
        $customer = auth('web')->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'location_city' => ['nullable', 'string', 'max:120'],
        ]);

        $customer->update($data);

        return back()->with('status', 'Profile updated.');
    }
}
