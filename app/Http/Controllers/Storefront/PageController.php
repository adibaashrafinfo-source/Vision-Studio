<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\View\View;

class PageController extends Controller
{
    public function faq(): View
    {
        return view('storefront.pages.faq');
    }

    public function shipping(): View
    {
        return view('storefront.pages.shipping');
    }

    public function returns(): View
    {
        return view('storefront.pages.returns');
    }

    public function privacy(): View
    {
        return view('storefront.pages.privacy');
    }

    public function terms(): View
    {
        return view('storefront.pages.terms');
    }

    public function stores(): View
    {
        $address = SiteSetting::get('contact_address');
        $phone = SiteSetting::get('contact_phone');
        $email = SiteSetting::get('contact_email');

        return view('storefront.pages.stores', compact('address', 'phone', 'email'));
    }

    public function help(): View
    {
        return view('storefront.pages.help');
    }
}
