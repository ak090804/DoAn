@extends('admin.layouts.admin')

@section('title', 'Phiếu nhập')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between mb-3">
        <h4>Phiếu nhập</h4>
        <a href="{{ route('admin.import-notes.create') }}" class="btn btn-success">Tạo phiếu nhập</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nhân viên</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($notes as $note)
                    <tr>
                        <td>{{ $note->id }}</td>
                        <td>{{ $note->employee->name ?? 'N/A' }}</td>
                        <td>{{ number_format($note->total_price, 0, ',', '.') }}đ</td>
                        <td>{{ $note->status }}</td>
                        <td>{{ $note->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.import-notes.show', $note) }}" class="btn btn-info btn-sm">Chi tiết</a>
                            <a href="{{ route('admin.import-notes.edit', $note) }}" class="btn btn-warning btn-sm">Sửa</a>
                            <form action="{{ route('admin.import-notes.destroy', $note) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm" onclick="return confirm('Xóa?')">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center">Không có phiếu nhập</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">{{ $notes->links() }}</div>
</div>
@endsection
