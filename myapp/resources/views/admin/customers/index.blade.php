@extends('admin.layouts.admin')

@section('title', 'Admin Customers')

@section('content')
<div class="container mt-4">

    <!-- Breadcrumbs -->
    <div class="row mb-4">
        <div class="col-sm-4">
            <h4 class="page-title" style="color: black;">Customers</h4>
        </div>
        <div class="col-sm-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Customers</li>
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
        <form action="{{ route('admin.customers.index') }}" method="GET" class="d-flex">
            <input type="text" name="q" class="form-control me-2" placeholder="Tìm theo tên hoặc điện thoại"
                value="{{ request('q') }}">
        </form>


        <a href="{{ route('admin.customers.create') }}" class="btn btn-success">Thêm khách hàng</a>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $customer)
                <tr>
                    <td>{{ $customer->id }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->user->email ?? '-' }}</td>
                    <td>{{ $customer->phone }}</td>
                    <td>{{ $customer->address }}</td>
                    <td>
                        <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-info btn-sm">Xem</a>
                        <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-warning btn-sm">Sửa</a>
                        <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Xóa</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">Không có khách hàng</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $customers->links() }}

</div>
@endsection
