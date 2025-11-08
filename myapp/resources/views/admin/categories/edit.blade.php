@extends('admin.layouts.admin')

@section('title', 'Chỉnh sửa danh mục')

@section('content')

<div class="container mt-4">

    <!-- Breadcrumbs -->
    <div class="row mb-4">
        <div class="col-sm-4">
            <h4 class="page-title" style="color: black;">Edit Categories</h4>
        </div>
        <div class="col-sm-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ url('/admin/categories') }}">Categories</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Thông báo lỗi hoặc thành công --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Lỗi!</strong> Vui lòng kiểm tra lại dữ liệu nhập vào.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form cập nhật --}}
    <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" class="card p-4 shadow-sm">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Tên danh mục</label>
            <input 
                type="text" 
                name="name" 
                id="name" 
                value="{{ old('name', $category->name) }}" 
                class="form-control @error('name') is-invalid @enderror" 
                placeholder="Nhập tên danh mục">

            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">⬅ Quay lại</a>
            <button type="submit" class="btn btn-primary">💾 Cập nhật</button>
        </div>
    </form>

</div>
@endsection
