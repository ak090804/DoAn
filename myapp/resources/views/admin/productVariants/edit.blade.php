@extends('admin.layouts.admin')

@section('title', 'Sửa biến thể sản phẩm')

@section('content')
<div class="container mt-4">

    <div class="row mb-4">
        <div class="col-sm-4">
            <h4 class="page-title" style="color: black;">Edit Product Variant</h4>
        </div>
        <div class="col-sm-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ url('/admin/productVariants') }}">Product Variants</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
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

    <form action="{{ route('admin.productVariants.update', $productVariant) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="product_id" class="form-label">Sản phẩm</label>
            <select name="product_id" id="product_id" class="form-select" required>
                <option value="">Chọn sản phẩm</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ old('product_id', $productVariant->product_id) == $product->id ? 'selected' : '' }}>
                        {{ $product->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="brand" class="form-label">Thương hiệu</label>
            <input type="text" name="brand" id="brand" class="form-control" value="{{ old('brand', $productVariant->brand) }}" required>
        </div>

        <div class="mb-3">
            <label for="supplier_id" class="form-label">Nhà cung cấp</label>
            <select name="supplier_id" id="supplier_id" class="form-select">
                <option value="">-- Không --</option>
                @php $suppliers = \App\Models\Supplier::all(); @endphp
                @foreach($suppliers as $sup)
                    <option value="{{ $sup->id }}" {{ old('supplier_id', $productVariant->supplier_id) == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="attribute" class="form-label">Attribute</label>
            <input type="text" name="attribute" id="attribute" class="form-control" value="{{ old('attribute', $productVariant->attribute) }}" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Mô tả</label>
            <textarea name="description" id="description" class="form-control" required>{{ old('description', $productVariant->description) }}</textarea>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Giá</label>
            <input type="number" name="price" id="price" class="form-control" value="{{ old('price', $productVariant->price) }}" required>
        </div>

        <div class="mb-3">
            <label for="quantity" class="form-label">Số lượng</label>
            <input type="number" name="quantity" id="quantity" class="form-control" value="{{ old('quantity', $productVariant->quantity) }}" required>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Ảnh</label>
            <input type="file" name="image" id="image" class="form-control">
            @if($productVariant->image)
                <div class="mt-2">
                    <img src="{{ asset('storage/products/' . $productVariant->image) }}" width="100" alt="Current Image">
                </div>
            @endif
        </div>

        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="{{ route('admin.productVariants.index') }}" class="btn btn-secondary">Hủy</a>
    </form>

</div>
@endsection
