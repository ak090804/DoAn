@extends('admin.layouts.admin')

@section('title', 'Employee Details')

@section('content')
<div class="container mt-4">

    <div class="row mb-4">
        <div class="col-sm-6">
            <h4 class="page-title" style="color: black;">Chi tiết nhân viên</h4>
        </div>
        <div class="col-sm-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Bảng Điều Khiển</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.employees.index') }}">Nhân Viên</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ $employee->name }}</h5>
            <p class="card-text"><strong>Email:</strong> {{ $employee->email }}</p>
            <p class="card-text"><strong>Phone:</strong> {{ $employee->phone }}</p>
            <p class="card-text"><strong>Address:</strong> {{ $employee->address }}</p>
            <p class="card-text"><strong>Position:</strong> {{ $employee->position }}</p>
            <p class="card-text"><strong>Salary:</strong> {{ $employee->salary }}</p>
            <p class="card-text"><strong>Hired At:</strong> {{ $employee->hired_at?->format('Y-m-d') }}</p>
            <a href="{{ route('admin.employees.edit', $employee) }}" class="btn btn-warning">Sửa</a>
            <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary">Quay lại</a>
        </div>
    </div>

</div>
@endsection
