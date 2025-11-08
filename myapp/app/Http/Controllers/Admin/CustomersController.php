<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\User;

class CustomersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Customer::with('users');

        // simple search by name or phone
        if ($request->filled('q')) {
            $q = $request->get('q');
            $query->where(function ($r) use ($q) {
                $r->where('name', 'like', "%{$q}%")
                  ->orWhere('phone', 'like', "%{$q}%");
            });
        }

        $customers = $query->latest()->paginate(15)->appends($request->query());

        return view('ListCustomers', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        return view('customers.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50|unique:customers,phone',
            'address' => 'nullable|string|max:500',
            'user_id' => 'nullable|exists: ,id',
        ]);

        Customer::create($data);

        return redirect()->route('customers.index')->with('success', 'Khách hàng đã được tạo.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50|unique:customers,phone,' . $customer->id,
            'address' => 'nullable|string|max:500',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $customer->update($data);

        return redirect()->route('customers.index')->with('success', 'Khách hàng đã được cập nhật.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Khách hàng đã bị xóa.');
    }
}
