@extends('admin.layouts.admin')

@section('title', 'Quản lý đơn hàng')

@section('content')
<div class="container mt-4">
    <!-- Breadcrumbs -->
    <div class="row mb-4">
        <div class="col-sm-4">
            <h4 class="page-title" style="color: black;">Đơn hàng</h4>
        </div>
        <div class="col-sm-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Đơn hàng</li>
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

    <!-- Filters -->
    <div class="mb-3">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="row g-3">
            <div class="col-md-2">
                <input type="text" name="search" class="form-control" placeholder="Tìm theo tên khách hàng" 
                    value="{{ $filters['search'] ?? '' }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">Trạng thái</option>
                    <option value="pending" {{ ($filters['status'] ?? '') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                    <option value="confirmed" {{ ($filters['status'] ?? '') == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                    <option value="shipping" {{ ($filters['status'] ?? '') == 'shipping' ? 'selected' : '' }}>Đang giao</option>
                    <option value="completed" {{ ($filters['status'] ?? '') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                    <option value="cancelled" {{ ($filters['status'] ?? '') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="supplier_id" class="form-select">
                    <option value="">Nhà cung cấp</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ ($filters['supplier_id'] ?? '') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control" placeholder="Từ ngày"
                    value="{{ $filters['date_from'] ?? '' }}">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" class="form-control" placeholder="Đến ngày"
                    value="{{ $filters['date_to'] ?? '' }}">
            </div>
            <div class="col-md-1">
                <select name="sort" class="form-select">
                    <option value="">Sắp xếp</option>
                    <option value="date_desc" {{ ($filters['sort'] ?? '') == 'date_desc' ? 'selected' : '' }}>Mới nhất</option>
                    <option value="date_asc" {{ ($filters['sort'] ?? '') == 'date_asc' ? 'selected' : '' }}>Cũ nhất</option>
                    <option value="total_desc" {{ ($filters['sort'] ?? '') == 'total_desc' ? 'selected' : '' }}>Tổng tiền ↓</option>
                    <option value="total_asc" {{ ($filters['sort'] ?? '') == 'total_asc' ? 'selected' : '' }}>Tổng tiền ↑</option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary">Lọc</button>
            </div>
        </form>
    </div>

    <div class="mb-3 d-flex justify-content-between">
        <div></div>
        <a href="{{ route('admin.orders.create') }}" class="btn btn-success">Tạo đơn hàng</a>
    </div>

    <!-- Orders table -->
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Khách hàng</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th style="width: 180px">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->customer->name ?? 'N/A' }}</td>
                        <td>{{ number_format($order->total_price, 0, ',', '.') }}đ</td>
                        <td>
                            <span class="badge bg-{{ $order->status == 'completed' ? 'success' : 
                                ($order->status == 'cancelled' ? 'danger' : 
                                ($order->status == 'shipping' ? 'info' : 
                                ($order->status == 'confirmed' ? 'primary' : 'warning'))) }}">
                                {{ $order->status_label }}
                            </span>
                        </td>
                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-info btn-sm">Chi tiết</a>
                            <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-warning btn-sm">Sửa</a>
                            @if($order->status != 'completed' && $order->status != 'cancelled')
                                <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" class="d-inline" 
                                    onsubmit="return confirm('Bạn có chắc muốn xóa đơn hàng này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Xóa</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Không có đơn hàng nào</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $orders->links() }}
    </div>
</div>
@endsection