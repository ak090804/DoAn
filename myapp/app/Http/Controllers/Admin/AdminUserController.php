<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    // Hiển thị danh sách user
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->input('search'),
            'role'   => $request->input('role'),
        ];

        $perPage = $request->input('per_page', 10);
        $users = $this->userService->getAllPaginated($perPage, $filters);

        return view('admin.users.index', compact('users', 'filters'));
    }

    // Form thêm mới
    public function create()
    {
        return view('admin.users.create');
    }

    // Tạo mới user
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => ['nullable', Rule::in(['admin', 'staff', 'customer'])],
        ]);

        $this->userService->create($validated);

        return redirect()->route('admin.users.index')->with('success', 'Thêm tài khoản thành công.');
    }

    // Form chỉnh sửa
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    // Show user details
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    // Cập nhật thông tin user
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => 'sometimes|string|max:255',
            'email'    => ['sometimes', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'sometimes|nullable|string|min:6',
            'role'     => ['sometimes', Rule::in(['admin', 'staff', 'customer'])],
        ]);

        $this->userService->update($user, $validated);

        return redirect()->route('admin.users.index')->with('success', 'Sửa tài khoản thành công.');
    }

    // Xóa user
    public function destroy(User $user)
    {
        $result = $this->userService->delete($user);

        if ($result['success']) {
            return redirect()->route('admin.users.index')->with('success', 'Xóa tài khoản thành công.');
        } else {
            return redirect()->route('admin.users.index')->with('error', $result['message']);
        }
    }
}
