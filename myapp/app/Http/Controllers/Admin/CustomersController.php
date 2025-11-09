<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CustomersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Customer::with('user');

        // simple search by name or phone
        if ($request->filled('q')) {
            $q = $request->get('q');
            $query->where(function ($r) use ($q) {
                $r->where('name', 'like', "%{$q}%")
                  ->orWhere('phone', 'like', "%{$q}%");
            });
        }

        $customers = $query->latest()->paginate(15)->appends($request->query());

        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.customers.create');
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
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        // create linked user with role customer
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'customer',
        ]);

        Customer::create([
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'user_id' => $user->id,
        ]);

        return redirect()->route('admin.customers.index')->with('success', 'Khách hàng đã được tạo và liên kết tài khoản.');
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
            'email' => 'nullable|email|unique:users,email,' . ($customer->user_id ?? 'NULL'),
            'password' => 'nullable|string|min:6',
        ]);

        // If customer already linked to a user, update that user
        if ($customer->user_id) {
            $user = User::find($customer->user_id);
            if ($user) {
                $user->name = $data['name'];
                if (!empty($data['email'])) {
                    $user->email = $data['email'];
                }
                if (!empty($data['password'])) {
                    $user->password = Hash::make($data['password']);
                }
                $user->save();
            }
        } else {
            // If not linked but email+password provided, create user and link
            if (!empty($data['email']) && !empty($data['password'])) {
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'role' => 'customer',
                ]);
                $customer->user_id = $user->id;
            }
        }

        $customer->name = $data['name'];
        $customer->phone = $data['phone'] ?? $customer->phone;
        $customer->address = $data['address'] ?? $customer->address;
        $customer->save();

        return redirect()->route('admin.customers.index')->with('success', 'Khách hàng đã được cập nhật.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function show(Customer $customer)
    {
        return view('admin.customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        // If the customer has a linked user, delete the user first (this will cascade-delete the customer via FK)
        if ($customer->user_id) {
            $user = User::find($customer->user_id);
            if ($user) {
                $user->delete();
                return redirect()->route('admin.customers.index')->with('success', 'Khách hàng và tài khoản người dùng đã bị xóa.');
            }
        }

        // Otherwise just delete the customer
        $customer->delete();
        return redirect()->route('admin.customers.index')->with('success', 'Khách hàng đã bị xóa.');
    }
}
