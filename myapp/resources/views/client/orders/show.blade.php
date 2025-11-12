@extends('client.layouts.ecommerce')

@section('title', 'Chi tiết đơn hàng')

@section('content')
    <div class="container mt-5">
        <h3>Chi tiết đơn hàng #{{ $order->id }}</h3>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card mt-3 mb-3">
            <div class="card-body">
                <p><strong>Trạng thái:</strong> {{ $order->status_label }}</p>
                <p><strong>Ngày tạo:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Tổng tiền:</strong> {{ number_format($order->total_price, 0, ',', '.') }}₫</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Chi tiết sản phẩm</div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderItems as $item)
                            <tr>
                                <td>{{ $item->productVariant->product->name ?? 'Sản phẩm' }}</td>
                                <td>{{ number_format($item->price, 0, ',', '.') }}₫</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->subtotal, 0, ',', '.') }}₫</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            <a href="{{ route('client.orders.index') }}" class="btn btn-secondary">Quay lại</a>

            @if($order->status == 'shipping')
                <form action="{{ route('client.orders.updateStatus', $order->id) }}" method="POST" class="d-inline ms-2">
                    @csrf
                    <input type="hidden" name="status" value="completed">
                    <button type="submit" class="btn btn-success">Đã nhận hàng</button>
                </form>
            @endif

            @if($order->status == 'pending')
                <form action="{{ route('client.orders.updateStatus', $order->id) }}" method="POST" class="d-inline ms-2">
                    @csrf
                    <input type="hidden" name="status" value="cancelled">
                    <button type="submit" class="btn btn-danger"
                        onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này?');">Hủy đơn</button>
                </form>
            @endif
        </div>
    </div>
@endsection