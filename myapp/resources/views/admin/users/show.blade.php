@extends('admin.layouts.admin')

@section('title', 'Xem người dùng')

@section('content')
<div class="container mt-4">
    <h4>Người dùng #{{ $user->id }}</h4>

    <div class="card mb-3">
        <div class="card-body">
            <p><strong>Tên:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Vai trò:</strong> {{ $user->role }}</p>
            <p><strong>Ngày tạo:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Quay lại</a>
    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning btn-edit">Sửa</a>
    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button class="btn btn-danger btn-delete" onclick="return confirm('Bạn có chắc muốn xóa người dùng này?')">Xóa</button>
    </form>
</div>
@endsection