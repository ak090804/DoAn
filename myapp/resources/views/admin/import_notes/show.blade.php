@extends('admin.layouts.admin')

@section('title', 'Chi tiết phiếu nhập')

@section('content')
<div class="container mt-4">
    <h4>Phiếu nhập #{{ $note->id }}</h4>

    <div class="card mb-3">
        <div class="card-body">
            <p><strong>Thu ngân:</strong> {{ $note->employee->name ?? 'N/A' }}</p>
            <p><strong>Trạng thái:</strong> {{ $note->status }}</p>
            <p><strong>Ghi chú:</strong> {{ $note->note ?? '-' }}</p>
            <p><strong>Ngày tạo:</strong> {{ $note->created_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Chi tiết sản phẩm</div>
        <div class="card-body">
            <table class="table">
                <thead><tr><th>Sản phẩm</th><th>Đơn giá</th><th>Số lượng</th><th>Thành tiền</th></tr></thead>
                <tbody>
                    @foreach($note->items as $it)
                        <tr>
                            <td>{{ $it->product->name }}</td>
                            <td>{{ number_format($it->price,0,',','.') }}đ</td>
                            <td>{{ $it->quantity }}</td>
                            <td>{{ number_format($it->subtotal,0,',','.') }}đ</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr><td colspan="3" class="text-end"><strong>Tổng:</strong></td><td><strong>{{ number_format($note->total_price,0,',','.') }}đ</strong></td></tr>
                </tfoot>
            </table>
        </div>
    </div>

    <a href="{{ route('admin.import-notes.index') }}" class="btn btn-secondary">Quay lại</a>
    <a href="{{ route('admin.import-notes.edit', $note) }}" class="btn btn-warning">Sửa</a>
    <form action="{{ route('admin.import-notes.destroy', $note) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button class="btn btn-danger">Xóa</button>
    </form>
</div>
@endsection
