<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Services\EmployeeService;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
	protected $service;

	public function __construct(EmployeeService $service)
	{
		$this->service = $service;
	}

	// Danh sách nhân viên
	public function index(Request $request)
	{
		$filters = $request->only(['search', 'sort']);
		$employees = $this->service->getAllPaginated(10, $filters);

		return view('admin.employees.index', compact('employees', 'filters'));
	}

	// Form thêm mới
	public function create()
	{
		return view('admin.employees.create');
	}

	// Lưu nhân viên mới
	public function store(Request $request)
	{
		$request->validate([
			'name' => 'required|string|max:255',
			'phone' => 'nullable|string|max:50',
			'address' => 'nullable|string|max:500',
			'position' => 'required|in:Thu ngân,Tiếp thị,Kiểm kho',
			'salary' => 'nullable|numeric|min:0',
			'hired_at' => 'nullable|date',
		]);

		$data = $request->only(['name','phone','address','position','salary','hired_at']);

		// If Thu ngân, require email and password and create linked user
		if ($request->input('position') === 'Thu ngân') {
			$request->validate([
				'email' => 'required|email|unique:users,email',
				'password' => 'required|string|min:6',
			]);

			$user = User::create([
				'name' => $request->input('name'),
				'email' => $request->input('email'),
				'password' => Hash::make($request->input('password')),
				'role' => 'staff',
			]);

			$data['user_id'] = $user->id;
			$data['email'] = $user->email;
		} else {
			$data['user_id'] = null;
			$data['email'] = $request->input('email');
		}

		$this->service->create($data);

		return redirect()->route('admin.employees.index')->with('success', 'Thêm nhân viên thành công.');
	}

	// Form chỉnh sửa
	public function edit(Employee $employee)
	{
		return view('admin.employees.edit', compact('employee'));
	}

	// Hiển thị chi tiết nhân viên
	public function show(Employee $employee)
	{
		return view('admin.employees.show', compact('employee'));
	}

	// Cập nhật nhân viên
	public function update(Request $request, Employee $employee)
	{
		$request->validate([
			'name' => 'required|string|max:255',
			'phone' => 'nullable|string|max:50',
			'address' => 'nullable|string|max:500',
			'position' => 'required|in:Thu ngân,Tiếp thị,Kiểm kho',
			'salary' => 'nullable|numeric|min:0',
			'hired_at' => 'nullable|date',
		]);

		$data = $request->only(['name','phone','address','position','salary','hired_at']);

		$newPosition = $request->input('position');

		// If new position is Thu ngân
		if ($newPosition === 'Thu ngân') {
			// If employee has no linked user, require email and password to create
			if (!$employee->user_id) {
				$request->validate([
					'email' => 'required|email|unique:users,email',
					'password' => 'required|string|min:6',
				]);

				$user = User::create([
					'name' => $request->input('name'),
					'email' => $request->input('email'),
					'password' => Hash::make($request->input('password')),
					'role' => 'staff',
				]);

				$data['user_id'] = $user->id;
				$data['email'] = $user->email;
			} else {
				// Has user: allow updating email/password
				$request->validate([
					'email' => 'nullable|email|unique:users,email,' . $employee->user_id,
					'password' => 'nullable|string|min:6',
				]);

				$user = User::find($employee->user_id);
				if ($user) {
					if ($request->filled('email')) {
						$user->email = $request->input('email');
					}
					if ($request->filled('password')) {
						$user->password = Hash::make($request->input('password'));
					}
					$user->name = $request->input('name');
					$user->save();
					$data['user_id'] = $user->id;
					$data['email'] = $user->email;
				}
			}
		} else {
			// New position is not Thu ngân: if employee had linked user, delete the user account
			if ($employee->user_id) {
				$user = User::find($employee->user_id);
				if ($user) {
					$user->delete();
				}
				$data['user_id'] = null;
			}
			$data['email'] = $request->input('email');
		}

		$this->service->update($employee, $data);

		return redirect()->route('admin.employees.index')->with('success', 'Cập nhật nhân viên thành công.');
	}

	// Xóa nhân viên
	public function destroy(Employee $employee)
	{
		$result = $this->service->delete($employee);

		if ($result['success']) {
			return redirect()->route('admin.employees.index')->with('success', 'Xóa nhân viên thành công.');
		} else {
			return redirect()->route('admin.employees.index')->with('error', $result['message']);
		}
	}
}

