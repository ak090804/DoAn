<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant as ProductVariant;
use App\Models\Employee;
use App\Services\ImportNoteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        // determine current admin user and role
        $adminUserId = $request->session()->get('admin_user_id') ?? optional(auth()->user())->id;
        $currentUser = $adminUserId ? \App\Models\User::find($adminUserId) : auth()->user();
        $currentRole = optional($currentUser)->role;
        // find employee record linked to current user (if any)
        $currentEmployee = null;
        if ($currentUser) {
            $currentEmployee = \App\Models\Employee::where('user_id', $currentUser->id)->first();
        }

        return view('admin.import_notes.index', compact('notes', 'filters', 'currentRole', 'currentEmployee'));
    }

    public function create()
    {
        // load only employees that are inventory (linked user role = inventory)
        $employees = Employee::whereHas('user', function($q){ $q->where('role', 'inventory'); })->get();
        $productVariants = ProductVariant::with('product')->get();

        // current admin user/employee
        $adminUserId = request()->session()->get('admin_user_id') ?? optional(auth()->user())->id;
        $currentUser = $adminUserId ? \App\Models\User::find($adminUserId) : auth()->user();
        $currentEmployee = null;
        if ($currentUser) {
            $currentEmployee = \App\Models\Employee::where('user_id', $currentUser->id)->first();
        }

        $defaultStatus = null;
        if ($currentUser && optional($currentUser)->role === 'inventory') {
            $defaultStatus = 'pending';
        }

        return view('admin.import_notes.create', compact('employees', 'productVariants', 'currentUser', 'currentEmployee', 'defaultStatus'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'items' => 'required|array|min:1',
                'items.*.product_variant_id' => 'required|exists:product_variants,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
                'status' => 'nullable|in:pending,approved',
                'note' => 'nullable|string',
            ]);

            $note = $this->service->create($validated);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'redirect' => route('admin.import-notes.show', $note),
                    'message' => 'Phiếu nhập đã được tạo.'
                ], 200);
            }

            return redirect()->route('admin.import-notes.show', $note)->with('success', 'Phiếu nhập đã được tạo.');
        } catch (\Exception $e) {
            Log::error('Error creating import note: ' . $e->getMessage(), ['exception' => $e]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi khi tạo phiếu: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', 'Không thể tạo phiếu nhập: Lỗi khi tạo phiếu.');
        }
    }

    public function show($id)
    {
        $note = $this->service->find($id);
        $adminUserId = request()->session()->get('admin_user_id') ?? optional(auth()->user())->id;
        $currentUser = $adminUserId ? \App\Models\User::find($adminUserId) : auth()->user();
        $currentRole = optional($currentUser)->role;
        $currentEmployee = null;
        if ($currentUser) {
            $currentEmployee = \App\Models\Employee::where('user_id', $currentUser->id)->first();
        }

        return view('admin.import_notes.show', compact('note', 'currentRole', 'currentEmployee'));
    }

    public function edit($id)
    {
        $note = $this->service->find($id);
        $employees = Employee::whereHas('user', function($q){ $q->where('role', 'inventory'); })->get();
        $productVariants = ProductVariant::with('product')->get();

        $adminUserId = request()->session()->get('admin_user_id') ?? optional(auth()->user())->id;
        $currentUser = $adminUserId ? \App\Models\User::find($adminUserId) : auth()->user();
        $currentEmployee = null;
        if ($currentUser) {
            $currentEmployee = \App\Models\Employee::where('user_id', $currentUser->id)->first();
        }

        // Only allow edit if admin or owner inventory
        if ($currentUser && optional($currentUser)->role === 'inventory') {
            if (!$currentEmployee || $note->employee_id != $currentEmployee->id) {
                return redirect()->route('admin.import-notes.index')->with('error', 'Bạn không có quyền sửa phiếu này.');
            }
        }

        return view('admin.import_notes.create', compact('note', 'employees', 'productVariants', 'currentUser', 'currentEmployee'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'items' => 'sometimes|array|min:1',
            'items.*.product_variant_id' => 'required|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'status' => 'required|in:pending,approved',
            'note' => 'nullable|string',
        ]);

        // permission check: only admin or owner can update
        $adminUserId = $request->session()->get('admin_user_id') ?? optional(auth()->user())->id;
        $currentUser = $adminUserId ? \App\Models\User::find($adminUserId) : auth()->user();
        $currentEmployee = null;
        if ($currentUser) { $currentEmployee = \App\Models\Employee::where('user_id', $currentUser->id)->first(); }
        $note = $this->service->find($id);
        if ($currentUser && $currentUser->role === 'inventory') {
            if (!$currentEmployee || $note->employee_id != $currentEmployee->id) {
                return redirect()->route('admin.import-notes.index')->with('error', 'Bạn không có quyền cập nhật phiếu này.');
            }
        }

        $note = $this->service->update($id, $validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => route('admin.import-notes.show', $note),
                'message' => 'Phiếu nhập đã được cập nhật.'
            ]);
        }

        return redirect()->route('admin.import-notes.show', $note)->with('success', 'Phiếu nhập đã được cập nhật.');
    }

    public function destroy($id)
    {
        // permission check: only admin or owner can delete
        $adminUserId = request()->session()->get('admin_user_id') ?? optional(auth()->user())->id;
        $currentUser = $adminUserId ? \App\Models\User::find($adminUserId) : auth()->user();
        $currentEmployee = null;
        if ($currentUser) { $currentEmployee = \App\Models\Employee::where('user_id', $currentUser->id)->first(); }
        $note = $this->service->find($id);
        if ($currentUser && $currentUser->role === 'inventory') {
            if (!$currentEmployee || $note->employee_id != $currentEmployee->id) {
                return redirect()->route('admin.import-notes.index')->with('error', 'Bạn không có quyền xóa phiếu này.');
            }
        }

        $this->service->delete($id);
        return redirect()->route('admin.import-notes.index')->with('success', 'Phiếu nhập đã bị xóa.');
    }
    
    public function approve(Request $request, $id)
    {
        try {
            // Temporary debug logging to diagnose approve request issues
            Log::info('Approve called', [
                'id' => $id,
                'path' => $request->path(),
                'method' => $request->method(),
                'session_all' => $request->session()->all(),
                'session_admin_user_id' => $request->session()->get('admin_user_id'),
                'auth_user_id' => optional(auth()->user())->id,
                'cookies' => isset($_COOKIE) ? $_COOKIE : null,
                'headers' => $request->headers->all(),
            ]);
            $note = $this->service->find($id);

            if ($note->status !== 'pending') {
                $msg = 'Chỉ có phiếu trạng thái chờ mới được duyệt.';
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 400);
                }
                return redirect()->back()->with('error', $msg);
            }

            DB::beginTransaction();
            // increment stock for each product variant
            foreach ($note->items as $it) {
                $pv = $it->product; // product() now returns ProductVariant
                if ($pv) {
                    $pv->quantity = ($pv->quantity ?? 0) + ($it->quantity ?? 0);
                    $pv->save();
                }
            }

            $note->status = 'approved';
            $note->save();
            DB::commit();

            $successMsg = 'Phiếu nhập đã được duyệt thành công!';
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMsg
                ], 200);
            }

            return redirect()->back()->with('success', $successMsg);
        } catch (\Exception $e) {
            Log::error('Error approving import note: ' . $e->getMessage(), ['exception' => $e]);
            DB::rollBack();
            
            $errorMsg = 'Lỗi khi duyệt phiếu: ' . $e->getMessage();
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $errorMsg], 500);
            }
            
            return redirect()->back()->with('error', 'Không thể duyệt phiếu nhập.');
        }
    }
}
