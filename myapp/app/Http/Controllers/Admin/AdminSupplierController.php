<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class AdminSupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::paginate(20);
        return view('admin.suppliers.index', compact('suppliers'));
    }

    public function show(Supplier $supplier)
    {
        // Redirect to productVariants index with supplier filter
        return redirect()->route('admin.productVariants.index', ['supplier_id' => $supplier->id]);
    }

    public function create()
    {
        return view('admin.suppliers.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:suppliers,name']);
        Supplier::create($request->all());
        return redirect()->route('admin.suppliers.index')->with('success', 'Nhà cung cấp đã được tạo.');
    }

    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate(['name' => 'required|unique:suppliers,name,' . $supplier->id]);
        $supplier->update($request->all());
        return redirect()->route('admin.suppliers.index')->with('success', 'Nhà cung cấp đã được cập nhật.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('admin.suppliers.index')->with('success', 'Nhà cung cấp đã bị xóa.');
    }
}
