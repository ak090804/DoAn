@extends('admin.layouts.admin_login')

@section('content')
<div class="card">
    <div class="card-header">
        <i class="fas fa-user-shield"></i>Đăng Nhập Admin
    </div>
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('admin.login.post') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Mật Khẩu</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-login text-white">
                <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
            </button>
        </form>
    </div>
</div>
@endsection
