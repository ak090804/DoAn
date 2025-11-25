@extends('admin.layouts.admin')

@section('title', 'Thêm biến thể sản phẩm')

@section('content')
<div class="container mt-4">

    <div class="row mb-4">
        <div class="col-sm-4">
            <h4 class="page-title" style="color: black;">Create New Product Variant</h4>
        </div>
        <div class="col-sm-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Bảng Điều Khiển</a></li>
                    <li class="breadcrumb-item"><a href="{{ url('/admin/productVariants') }}">Biến Thể Sản Phẩm</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tạo</li>
                </ol>
            </nav>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.productVariants.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="product_id" class="form-label">Sản phẩm</label>
            <select name="product_id" id="product_id" class="form-select" required>
                <option value="">Chọn sản phẩm</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                        {{ $product->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="brand" class="form-label">Thương hiệu</label>
            <input type="text" name="brand" id="brand" class="form-control" value="{{ old('brand') }}" required>
        </div>

        <div class="mb-3">
            <label for="supplier_id" class="form-label">Nhà cung cấp</label>
            <select name="supplier_id" id="supplier_id" class="form-select">
                <option value="">-- Không --</option>
                @php $suppliers = \App\Models\Supplier::all(); @endphp
                @foreach($suppliers as $sup)
                    <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="attribute" class="form-label">Attribute</label>
            <input type="text" name="attribute" id="attribute" class="form-control" value="{{ old('attribute') }}" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Mô tả</label>
            <textarea name="description" id="description" class="form-control" required>{{ old('description') }}</textarea>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Giá</label>
            <input type="number" name="price" id="price" class="form-control" value="{{ old('price') }}" required>
        </div>

        <div class="mb-3">
            <label for="quantity" class="form-label">Số lượng</label>
            <input type="number" name="quantity" id="quantity" class="form-control" value="{{ old('quantity') }}" required>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Ảnh</label>
            <input type="file" name="image" id="image" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success">Thêm</button>
        <a href="{{ route('admin.productVariants.index') }}" class="btn btn-secondary">Hủy</a>
    </form>

</div>
@endsection
