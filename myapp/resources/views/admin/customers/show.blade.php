@extends('admin.layouts.admin')

@section('title', 'Chi tiết khách hàng')

@section('content')
<div class="container mt-4">
    <!-- Breadcrumbs -->
    <div class="row mb-4">
        <div class="col-sm-4">
            <h4 class="page-title" style="color: black;">Chi tiết khách hàng</h4>
        </div>
        <div class="col-sm-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Bảng Điều Khiển</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}">Khách hàng</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Thông tin khách hàng #{{ $customer->id }}</h5>
                <div>
                        <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-warning btn-edit">Sửa</a>
                        <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" class="d-inline" 
                              onsubmit="return confirm('Bạn có chắc muốn xóa khách hàng này?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-delete">Xóa</button>
                        </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th style="width: 200px">Tên khách hàng:</th>
                            <td>{{ $customer->name }}</td>
                        </tr>
                        <tr>
                            <th>Số điện thoại:</th>
                            <td>{{ $customer->phone ?: 'Chưa cập nhật' }}</td>
                        </tr>
                        <tr>
                            <th>Địa chỉ:</th>
                            <td>{{ $customer->address ?: 'Chưa cập nhật' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5 class="mb-3">Thông tin tài khoản</h5>
                    @if($customer->user)
                        <table class="table table-borderless">
                            <tr>
                                <th style="width: 200px">Email:</th>
                                <td>{{ $customer->user->email }}</td>
                            </tr>
                            <tr>
                                <th>Ngày tạo tài khoản:</th>
                                <td>{{ $customer->user->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Cập nhật lần cuối:</th>
                                <td>{{ $customer->user->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    @else
                        <p class="text-muted">Khách hàng chưa có tài khoản liên kết</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Orders section if needed in the future -->
    <!--
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">Lịch sử đơn hàng</h5>
        </div>
        <div class="card-body">
            <p class="text-muted">Feature will be implemented later</p>
        </div>
    </div>
    -->
</div>
@endsection