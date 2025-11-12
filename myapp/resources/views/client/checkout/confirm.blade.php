@extends('client.layouts.ecommerce')

@section('title', 'Xác nhận đơn hàng thành công')

@section('content')
    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Thành công -->
                <div class="alert alert-success text-center mb-4">
                    <i class="fas fa-check-circle fa-3x mb-3 d-block"></i>
                    <h3>Đơn hàng được tạo thành công!</h3>
                    <p class="mb-0">Cảm ơn bạn đã mua hàng</p>
                </div>

                <!-- Thông tin đơn hàng -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Thông tin đơn hàng #{{ $order->id }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Tên khách hàng:</strong>
                                <p>{{ $order->customer_name }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Email:</strong>
                                <p>{{ $order->customer_email }}</p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Số điện thoại:</strong>
                                <p>{{ $order->customer_phone }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Trạng thái:</strong>
                                <p>
                                    <span class="badge bg-warning">{{ ucfirst($order->status) }}</span>
                                </p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <strong>Địa chỉ giao hàng:</strong>
                            <p>{{ $order->customer_address }}</p>
                        </div>

                        @if($order->note)
                            <div class="mb-3">
                                <strong>Ghi chú:</strong>
                                <p>{{ $order->note }}</p>
                            </div>
                        @endif

                        <hr>

                        <div>
                            <strong>Ngày đặt hàng:</strong>
                            <p>{{ $order->created_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Chi tiết sản phẩm -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Chi tiết sản phẩm</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tên sản phẩm</th>
                                        <th>Giá</th>
                                        <th>Số lượng</th>
                                        <th>Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->orderItems as $item)
                                        <tr>
                                            <td>
                                                {{-- productVariant relation -> product name, fallback to variant name --}}
                                                {{ $item->productVariant->product->name ?? $item->productVariant->name ?? 'Sản phẩm' }}
                                            </td>
                                            <td class="text-end">{{ number_format($item->price, 0, ',', '.') }}₫</td>
                                            <td class="text-center">{{ $item->quantity }}</td>
                                            <td class="fw-bold text-end">{{ number_format($item->subtotal, 0, ',', '.') }}₫</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <hr>

                        <div class="text-end">
                            <strong class="fs-5">Tổng cộng:</strong>
                            <div class="fs-4 fw-bold text-primary">
                                {{ number_format($order->total_price, 0, ',', '.') }}₫
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Nút hành động -->
                <div class="d-flex gap-2">
                    <a href="{{ route('client.home') }}" class="btn btn-primary flex-fill">
                        <i class="fas fa-home"></i> Về trang chủ
                    </a>
                    <a href="{{ route('client.products') }}" class="btn btn-outline-primary flex-fill">
                        <i class="fas fa-shopping-bag"></i> Tiếp tục mua sắm
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection