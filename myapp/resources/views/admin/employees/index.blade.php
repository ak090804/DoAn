@extends('admin.layouts.admin')

@section('title', 'Admin Employees')

@section('content')
<div class="container mt-4">

    <!-- Breadcrumbs -->
    <div class="row mb-4">
        <div class="col-sm-4">
            <h4 class="page-title" style="color: black;">Employees</h4>
        </div>
        <div class="col-sm-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Employees</li>
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

    <div class="mb-3 d-flex justify-content-between">
        <form action="{{ route('admin.employees.index') }}" method="GET" class="d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Tìm theo tên, email hoặc điện thoại"
                value="{{ $filters['search'] ?? '' }}">
            <select name="sort" class="form-select me-2" onchange="this.form.submit()">
                <option value="">Sắp xếp</option>
                <option value="id_desc" {{ ($filters['sort'] ?? '') == 'id_desc' ? 'selected' : '' }}>Mới nhất</option>
                <option value="id_asc" {{ ($filters['sort'] ?? '') == 'id_asc' ? 'selected' : '' }}>Cũ nhất</option>
                <option value="name_asc" {{ ($filters['sort'] ?? '') == 'name_asc' ? 'selected' : '' }}>Tên A-Z</option>
                <option value="name_desc" {{ ($filters['sort'] ?? '') == 'name_desc' ? 'selected' : '' }}>Tên Z-A</option>
            </select>
        </form>


        <a href="{{ route('admin.employees.create') }}" class="btn btn-success">Thêm nhân viên</a>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Vị trí</th>
                <th>Hired At</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse($employees as $employee)
                <tr>
                    <td>{{ $employee->id }}</td>
                    <td>{{ $employee->name }}</td>
                    <td>{{ $employee->email }}</td>
                    <td>{{ $employee->phone }}</td>
                    <td>{{ $employee->position }}</td>
                    <td>{{ $employee->hired_at?->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('admin.employees.show', $employee) }}" class="btn btn-success btn-sm">Xem</a>
                        <a href="{{ route('admin.employees.edit', $employee) }}" class="btn btn-warning btn-sm">Sửa</a>
                        <form action="{{ route('admin.employees.destroy', $employee) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Xóa</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">Không có nhân viên</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $employees->links() }}

</div>
@endsection
