<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketingController extends Controller
{
    public function index(): View
    {
        $campaigns = Campaign::latest()->paginate(10);

        $stats = [
            'total' => Campaign::count(),
            'active' => Campaign::where('status', 'active')->count(),
            'audience' => (int) Campaign::sum('audience_count'),
            'scheduled' => Campaign::where('status', 'scheduled')->count(),
        ];

        return view('admin.marketing', compact('campaigns', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        Campaign::create($this->validateData($request));

        return redirect()->route('admin.marketing.index')->with('status', 'Campaign created.');
    }

    public function update(Request $request, Campaign $campaign): RedirectResponse
    {
        $campaign->update($this->validateData($request));

        return redirect()->route('admin.marketing.index')->with('status', 'Campaign updated.');
    }

    public function destroy(Campaign $campaign): RedirectResponse
    {
        $campaign->delete();

        return redirect()->route('admin.marketing.index')->with('status', 'Campaign deleted.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'channel' => ['nullable', 'string', 'max:60'],
            'audience_count' => ['nullable', 'integer', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'status' => ['required', 'in:draft,scheduled,active,ended'],
        ]);
    }
}
