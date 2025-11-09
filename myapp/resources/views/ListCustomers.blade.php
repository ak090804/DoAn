@extends('admin.layouts.admin')

@section('title', 'Customers')

@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-sm-6">
            <h4 class="page-title" style="color: black;">Khách hàng</h4>
        </div>
        <div class="col-sm-6 text-end">
            <a href="{{ route('customers.create') }}" class="btn btn-success">Thêm khách hàng</a>
        </div>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Has Account</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $c)
                <tr>
                    <td>{{ $c->id }}</td>
                    <td>{{ $c->name }}</td>
                    <td>{{ $c->user->email ?? '-' }}</td>
                    <td>{{ $c->phone }}</td>
                    <td>{{ $c->user_id ? 'Yes' : 'No' }}</td>
                    <td>
                        <a href="{{ route('customers.edit', $c) }}" class="btn btn-warning btn-sm">Sửa</a>
                        <form action="{{ route('customers.destroy', $c) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa?');">
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
