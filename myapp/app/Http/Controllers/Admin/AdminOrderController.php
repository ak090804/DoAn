<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        $filters = [
            'search' => $request->get('search'),
            'status' => $request->get('status'),
            'sort' => $request->get('sort'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
        ];

        $orders = $this->orderService->getAllPaginated(15, $filters);
        $employees = Employee::where('position', 'Thu ngân')->get();

        return view('admin.orders.index', compact('orders', 'filters', 'employees'));
    }

    public function create()
    {
        $customers = Customer::all();
        $employees = Employee::where('position', 'Thu ngân')->get();
        $products = Product::all();

        return view('admin.orders.create', compact('customers', 'employees', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'employee_id' => 'required|exists:employees,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'status' => 'nullable|in:pending,confirmed,shipping,completed,cancelled',
            'note' => 'nullable|string',
        ]);

        $order = $this->orderService->create($validated);

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Đơn hàng đã được tạo thành công.');
    }

    public function show($id)
    {
        $order = $this->orderService->find($id);
        return view('admin.orders.show', compact('order'));
    }

    public function edit($id)
    {
        $order = $this->orderService->find($id);
        $customers = Customer::all();
        $employees = Employee::where('position', 'Thu ngân')->get();
        $products = Product::all();

        return view('admin.orders.create', compact('order', 'customers', 'employees', 'products'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'status' => 'required|in:pending,confirmed,shipping,completed,cancelled',
            'items' => 'sometimes|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        $order = $this->orderService->update($id, $validated);

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Đơn hàng đã được cập nhật thành công.');
    }

    public function destroy($id)
    {
        $this->orderService->delete($id);

        return redirect()
            ->route('admin.orders.index')
            ->with('success', 'Đơn hàng đã được xóa thành công.');
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,shipping,completed,cancelled',
        ]);

        $this->orderService->updateStatus($id, $validated['status']);

        return redirect()
            ->route('admin.orders.show', $id)
            ->with('success', 'Trạng thái đơn hàng đã được cập nhật.');
    }
}