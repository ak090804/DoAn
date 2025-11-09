@extends('admin.layouts.admin')

@section('title', 'Tạo nhà cung cấp')

@section('content')
<div class="container mt-4">
    <h4>Tạo nhà cung cấp mới</h4>

    @if($errors->any())
        <div class="alert alert-danger"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <form action="{{ route('admin.suppliers.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Tên nhà cung cấp</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Điện thoại</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Website</label>
            <input type="text" name="website" class="form-control" value="{{ old('website') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Ghi chú</label>
            <textarea name="note" class="form-control">{{ old('note') }}</textarea>
        </div>

        <div class="text-end">
            <button class="btn btn-primary">Tạo</button>
            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary">Hủy</a>
        </div>
    </form>
</div>
@endsection