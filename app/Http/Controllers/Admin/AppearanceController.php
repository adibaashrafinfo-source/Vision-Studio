<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AppearanceController extends Controller
{
    /** Default brand theme (matches the reference design tokens). */
    public const DEFAULTS = [
        'theme_navy' => '#0A1A42',
        'theme_blue' => '#0C3F93',
        'theme_blue_bright' => '#2F6FD6',
        'theme_sale' => '#E0314A',
        'theme_paper' => '#F6F7FB',
    ];

    public function edit(): View
    {
        return view('admin.appearance', [
            's' => SiteSetting::allValues(),
            'defaults' => self::DEFAULTS,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'theme_navy' => ['nullable', 'string', 'max:9'],
            'theme_blue' => ['nullable', 'string', 'max:9'],
            'theme_blue_bright' => ['nullable', 'string', 'max:9'],
            'theme_sale' => ['nullable', 'string', 'max:9'],
            'theme_paper' => ['nullable', 'string', 'max:9'],
            'theme_preset' => ['nullable', 'string', 'max:40'],
            'logo' => ['nullable', 'image', 'max:4096'],
        ]);

        if ($request->hasFile('logo')) {
            if ($old = SiteSetting::get('logo_path')) {
                Storage::disk('public')->delete($old);
            }
            SiteSetting::set('logo_path', $request->file('logo')->store('site', 'public'), 'branding');
        }
        unset($data['logo']);

        foreach ($data as $key => $value) {
            SiteSetting::set($key, $value, $key === 'theme_preset' ? 'branding' : 'theme');
        }

        return back()->with('status', 'Appearance saved.');
    }
}
