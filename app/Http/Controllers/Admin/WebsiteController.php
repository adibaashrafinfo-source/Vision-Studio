<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class WebsiteController extends Controller
{
    public function edit(): View
    {
        return view('admin.website', ['s' => SiteSetting::allValues()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'hero_headline' => ['nullable', 'string', 'max:255'],
            'hero_subheadline' => ['nullable', 'string', 'max:500'],
            'hero_cta_text' => ['nullable', 'string', 'max:60'],
            'hero_cta_link' => ['nullable', 'string', 'max:255'],
            'announcement_text' => ['nullable', 'string', 'max:255'],
            'footer_about' => ['nullable', 'string', 'max:1000'],
            'footer_phone' => ['nullable', 'string', 'max:60'],
            'footer_email' => ['nullable', 'string', 'max:120'],
            'footer_address' => ['nullable', 'string', 'max:255'],
            'social_facebook' => ['nullable', 'string', 'max:255'],
            'social_instagram' => ['nullable', 'string', 'max:255'],
            'social_youtube' => ['nullable', 'string', 'max:255'],
            'social_tiktok' => ['nullable', 'string', 'max:255'],
            'social_linkedin' => ['nullable', 'string', 'max:255'],
            'hero_image' => ['nullable', 'image', 'max:8192'],
        ]);

        if ($request->hasFile('hero_image')) {
            if ($old = SiteSetting::get('hero_image')) {
                Storage::disk('public')->delete($old);
            }
            SiteSetting::set('hero_image', $request->file('hero_image')->store('site', 'public'), 'hero');
        }
        unset($data['hero_image']);

        foreach ($data as $key => $value) {
            $group = str_starts_with($key, 'hero_') ? 'hero'
                : (str_starts_with($key, 'social_') ? 'social'
                : (str_starts_with($key, 'footer_') ? 'footer' : 'general'));
            SiteSetting::set($key, $value, $group);
        }

        return back()->with('status', 'Website content saved.');
    }
}
