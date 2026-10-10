<?php

namespace App\Http\Controllers\Storefront\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CustomerRegisterController extends Controller
{
    public function show(): View
    {
        return view('storefront.auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:customers,email'],
            'phone' => ['nullable', 'string', 'max:40'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $customer = Customer::create($data + ['status' => 'active', 'customer_group' => 'regular']);

        Auth::guard('web')->login($customer);
        $request->session()->regenerate();

        return redirect()->route('account')->with('status', 'Welcome to Vision Studio!');
    }
}
