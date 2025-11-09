@extends('admin.layouts.admin')

@section('title', 'Admin Users')

@section('content')
<div class="container mt-4">

    <!-- Breadcrumbs -->
    <div class="row mb-4">
        <div class="col-sm-4">
            <h4 class="page-title" style="color: black;">Users</h4>
        </div>
        <div class="col-sm-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Users</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Alert session -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Search + Add -->
    <div class="mb-3 d-flex justify-content-between">
        <form action="{{ route('admin.users.index') }}" method="GET" class="d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Tìm theo tên hoặc email"
                value="{{ $filters['search'] ?? '' }}">
            <select name="role" class="form-select me-2" onchange="this.form.submit()">
                <option value="">Tất cả vai trò</option>
                <option value="admin" {{ ($filters['role'] ?? '') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="staff" {{ ($filters['role'] ?? '') == 'staff' ? 'selected' : '' }}>Nhân viên</option>
                <option value="customer" {{ ($filters['role'] ?? '') == 'customer' ? 'selected' : '' }}>Khách hàng</option>
            </select>
        </form>


        <a href="{{ route('admin.users.create') }}" class="btn btn-success">Thêm người dùng</a>
    </div>

    <!-- Users Table -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên</th>
                <th>Email</th>
                <th>Vai trò</th>
                <th>Ngày tạo</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'staff' ? 'info' : 'secondary') }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                        {{-- <a href="{{ route('admin.customers.index', ['user_id' => $user->id]) }}" class="btn btn-success btn-sm">Xem</a> --}}
                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-success btn-sm">Xem</a>
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning btn-sm">Sửa</a>
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa người dùng này?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Xóa</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Không có người dùng</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $users->links() }}
</div>
@endsection
