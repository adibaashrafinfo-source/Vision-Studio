<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomersController extends Controller
{
    public function index(Request $request): View
    {
        $query = Customer::query()->withCount('orders')->withSum('orders', 'total');

        if ($search = $request->string('q')->trim()->value()) {
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%"));
        }
        if (in_array($request->get('status'), ['active', 'inactive'], true)) {
            $query->where('status', $request->get('status'));
        }

        $customers = $query->latest()->paginate(12)->withQueryString();

        $stats = [
            'total' => Customer::count(),
            'active' => Customer::where('status', 'active')->count(),
            'new' => Customer::where('created_at', '>=', now()->startOfMonth())->count(),
            'inactive' => Customer::where('status', '!=', 'active')->count(),
        ];

        return view('admin.customers.index', compact('customers', 'stats'));
    }

    public function show(Customer $customer): View
    {
        $customer->load('addresses', 'orders');

        return view('admin.customers.show', compact('customer'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        Customer::create($data);

        return redirect()->route('admin.customers.index')->with('status', 'Customer created.');
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $data = $this->validateData($request, $customer);
        if (empty($data['password'])) {
            unset($data['password']);
        }
        $customer->update($data);

        return redirect()->route('admin.customers.index')->with('status', 'Customer updated.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()->route('admin.customers.index')->with('status', 'Customer deleted.');
    }

    private function validateData(Request $request, ?Customer $customer = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('customers', 'email')->ignore($customer?->id)],
            'phone' => ['nullable', 'string', 'max:40'],
            'password' => ['nullable', 'string', 'min:8'],
            'location_city' => ['nullable', 'string', 'max:120'],
            'gender' => ['nullable', 'in:male,female,other'],
            'customer_group' => ['nullable', 'string', 'max:40'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);
    }
}
