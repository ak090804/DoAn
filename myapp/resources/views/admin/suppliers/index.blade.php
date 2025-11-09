@extends('admin.layouts.admin')

@section('title', 'Nhà cung cấp')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between mb-3">
        <h4>Nhà cung cấp</h4>
        <a href="{{ route('admin.suppliers.create') }}" class="btn btn-success">Tạo nhà cung cấp</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên</th>
                    <th>Điện thoại</th>
                    <th>Email</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $s)
                <tr>
                    <td>{{ $s->id }}</td>
                    <td>{{ $s->name }}</td>
                    <td>{{ $s->phone }}</td>
                    <td>{{ $s->email }}</td>
                    <td>
                        <a href="{{ route('admin.suppliers.show', $s) }}" class="btn btn-info btn-sm">Xem sản phẩm</a>
                        <a href="{{ route('admin.suppliers.edit', $s) }}" class="btn btn-warning btn-sm">Sửa</a>
                        <form action="{{ route('admin.suppliers.destroy', $s) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Xóa?')">Xóa</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center">Không có nhà cung cấp</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">{{ $suppliers->links() }}</div>
</div>
@endsection