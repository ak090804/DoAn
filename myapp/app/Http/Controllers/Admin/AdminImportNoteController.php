<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Employee;
use App\Services\ImportNoteService;
use Illuminate\Http\Request;

class AdminImportNoteController extends Controller
{
    protected $service;

    public function __construct(ImportNoteService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = [
            'status' => $request->get('status'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
        ];

        $notes = $this->service->getAllPaginated(15, $filters);
        return view('admin.import_notes.index', compact('notes', 'filters'));
    }

    public function create()
    {
           $employees = Employee::all();
        $products = Product::all();
        return view('admin.import_notes.create', compact('employees', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'status' => 'nullable|in:pending,approved,cancelled',
            'note' => 'nullable|string',
        ]);

        $note = $this->service->create($validated);

        return redirect()->route('admin.import-notes.show', $note)->with('success', 'Phiếu nhập đã được tạo.');
    }

    public function show($id)
    {
        $note = $this->service->find($id);
        return view('admin.import_notes.show', compact('note'));
    }

    public function edit($id)
    {
        $note = $this->service->find($id);
            $employees = Employee::all();
        $products = Product::all();
        return view('admin.import_notes.create', compact('note', 'employees', 'products'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'items' => 'sometimes|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'status' => 'required|in:pending,approved,cancelled',
            'note' => 'nullable|string',
        ]);

        $note = $this->service->update($id, $validated);

        return redirect()->route('admin.import-notes.show', $note)->with('success', 'Phiếu nhập đã được cập nhật.');
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return redirect()->route('admin.import-notes.index')->with('success', 'Phiếu nhập đã bị xóa.');
    }
}
