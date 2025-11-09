@extends('admin.layouts.admin')

@section('title', 'Create Customer')

@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-sm-6">
            <h4 class="page-title" style="color: black;">Thêm khách hàng</h4>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.customers.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Tên</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email (bắt buộc)</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password (bắt buộc)</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-control">{{ old('address') }}</textarea>
        </div>

        <button class="btn btn-primary">Lưu</button>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection
