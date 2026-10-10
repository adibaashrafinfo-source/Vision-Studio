<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings', ['s' => SiteSetting::allValues()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'store_name' => ['nullable', 'string', 'max:255'],
            'store_email' => ['nullable', 'email', 'max:255'],
            'store_phone' => ['nullable', 'string', 'max:60'],
            'store_address' => ['nullable', 'string', 'max:255'],
            'currency' => ['nullable', 'string', 'max:10'],
            'currency_symbol' => ['nullable', 'string', 'max:10'],
            'free_shipping_threshold' => ['nullable', 'numeric', 'min:0'],
            'shipping_fee' => ['nullable', 'numeric', 'min:0'],
            'cod_enabled' => ['nullable', 'boolean'],
            'bkash_enabled' => ['nullable', 'boolean'],
            'nagad_enabled' => ['nullable', 'boolean'],
        ]);

        // Normalise unchecked toggles to 0.
        foreach (['cod_enabled', 'bkash_enabled', 'nagad_enabled'] as $toggle) {
            $data[$toggle] = $request->boolean($toggle) ? 1 : 0;
        }

        foreach ($data as $key => $value) {
            SiteSetting::set($key, $value, 'general');
        }

        return back()->with('status', 'Settings saved.');
    }
}
