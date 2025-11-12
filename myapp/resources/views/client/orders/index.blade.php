@extends('client.layouts.ecommerce')

@section('title', 'Lịch sử đơn hàng của tôi')

@section('content')
    <div class="container mt-5">
        <h3 class="mb-4">Lịch sử đơn hàng</h3>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($orders->isEmpty())
            <div class="alert alert-info">Bạn chưa có đơn hàng nào.</div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Ngày</th>
                            <th>Trạng thái</th>
                            <th>Tổng tiền</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $order->status_label }}</td>
                                <td>{{ number_format($order->total_price, 0, ',', '.') }}₫</td>
                                <td>
                                    <a href="{{ route('client.orders.show', $order->id) }}" class="btn btn-sm btn-info">Chi tiết</a>

                                    @if($order->status == 'shipping')
                                        <form action="{{ route('client.orders.updateStatus', $order->id) }}" method="POST"
                                            class="d-inline ms-1">
                                            @csrf
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="btn btn-sm btn-success">Đã nhận hàng</button>
                                        </form>
                                    @endif

                                    @if($order->status == 'pending')
                                        <form action="{{ route('client.orders.updateStatus', $order->id) }}" method="POST"
                                            class="d-inline ms-1">
                                            @csrf
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này?');">Hủy đơn</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $orders->links() }}
        @endif
    </div>
@endsection