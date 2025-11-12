@extends('admin.layouts.admin')

@section('title', 'Create Employee')

@section('content')
<div class="container mt-4">

    <div class="row mb-4">
        <div class="col-sm-6">
            <h4 class="page-title" style="color: black;">Thêm nhân viên</h4>
        </div>
        <div class="col-sm-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.employees.index') }}">Employees</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Thêm</li>
                </ol>
            </nav>
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

    <form action="{{ route('admin.employees.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Tên</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Vị trí</label>
            <select name="position" id="position-select" class="form-select" required>
                <option value="">-- Chọn vị trí --</option>
                <option value="Thu ngân" {{ old('position') == 'Thu ngân' ? 'selected' : '' }}>Thu ngân</option>
                <option value="Tiếp thị" {{ old('position') == 'Tiếp thị' ? 'selected' : '' }}>Tiếp thị</option>
                <option value="Kiểm kho" {{ old('position') == 'Kiểm kho' ? 'selected' : '' }}>Kiểm kho</option>
            </select>
        </div>

        <div id="cashier-fields" style="display: none;">
            <div class="mb-3">
                <label class="form-label">Email (bắt buộc cho Thu ngân hoặc Kiểm kho)</label>
                <input type="email" id="cashier-email" name="email" class="form-control" value="{{ old('email') }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Password (bắt buộc cho Thu ngân hoặc Kiểm kho)</label>
                <input type="password" id="cashier-password" name="password" class="form-control">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-control">{{ old('address') }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Salary</label>
            <input type="number" step="0.01" name="salary" class="form-control" value="{{ old('salary') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Hired At</label>
            <input type="date" name="hired_at" class="form-control" value="{{ old('hired_at') }}">
        </div>

        <button class="btn btn-primary">Lưu</button>
        <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary">Hủy</a>
    </form>

</div>
@endsection

@section('scripts')
<script>
    (function(){
        const select = document.getElementById('position-select');
        const cashierFields = document.getElementById('cashier-fields');
        const email = document.getElementById('cashier-email');
        const password = document.getElementById('cashier-password');

        function toggle() {
            if (select.value === 'Thu ngân' || select.value === 'Kiểm kho') {
                cashierFields.style.display = '';
                email.setAttribute('required', 'required');
                password.setAttribute('required', 'required');
            } else {
                cashierFields.style.display = 'none';
                email.removeAttribute('required');
                password.removeAttribute('required');
            }
        }

        select.addEventListener('change', toggle);
        // initial
        toggle();
    })();
</script>
@endsection
