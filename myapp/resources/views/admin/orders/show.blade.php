@extends('admin.layouts.admin')

@section('title', 'Chi tiết đơn hàng')

@section('content')
<div class="container mt-4">
    <!-- Breadcrumbs -->
    <div class="row mb-4">
        <div class="col-sm-6">
            <h4 class="page-title" style="color: black;">Chi tiết đơn hàng #{{ $order->id }}</h4>
        </div>
        <div class="col-sm-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Đơn hàng</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
                </ol>
            </nav>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Order Info -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Thông tin đơn hàng</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Khách hàng:</strong> {{ $order->customer->name }}</p>
                    <p><strong>Điện thoại:</strong> {{ $order->customer->phone ?? 'N/A' }}</p>
                    <p><strong>Địa chỉ:</strong> {{ $order->customer->address ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Ngày tạo:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Trạng thái:</strong> 
                        <span class="badge bg-{{ $order->status == 'completed' ? 'success' : 
                            ($order->status == 'cancelled' ? 'danger' : 
                            ($order->status == 'shipping' ? 'info' : 
                            ($order->status == 'confirmed' ? 'primary' : 'warning'))) }}">
                            {{ $order->status }}
                        </span>
                    </p>
                    <p><strong>Ghi chú:</strong> {{ $order->note ?? 'Không có' }}</p>
                    <p><strong>Thu ngân:</strong> {{ $order->employee ? $order->employee->name : 'Chưa phân công' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Items -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Chi tiết sản phẩm</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Đơn giá</th>
                            <th>Số lượng</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderItems as $item)
                            <tr>
                                <td>{{ $item->productVariant->product->name }} - {{ $item->productVariant->attribute }}</td>
                                <td>{{ number_format($item->price, 0, ',', '.') }}đ</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->subtotal, 0, ',', '.') }}đ</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Tổng cộng:</strong></td>
                            <td><strong>{{ number_format($order->total_price, 0, ',', '.') }}đ</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="card mb-4">
        <div class="card-body">
            @if($order->status != 'completed' && $order->status != 'cancelled')
                <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <div class="row g-3 align-items-center">
                        <div class="col-auto">
                            <select name="status" class="form-select">
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                                <option value="shipping" {{ $order->status == 'shipping' ? 'selected' : '' }}>Đang giao</option>
                                <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary">Cập nhật trạng thái</button>
                        </div>
                    </div>
                </form>
            @endif

            <div class="mt-3">
                <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-warning">Sửa đơn hàng</a>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Quay lại</a>
                @if($order->status != 'completed' && $order->status != 'cancelled')
                    <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" class="d-inline" 
                        onsubmit="return confirm('Bạn có chắc muốn xóa đơn hàng này?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger">Xóa đơn hàng</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection