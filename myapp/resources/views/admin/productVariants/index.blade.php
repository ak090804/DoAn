@extends('admin.layouts.admin')

@section('title', 'Quản lý biến thể sản phẩm')

@section('content')
<div class="container mt-4">

    <div class="row mb-4">
        <div class="col-sm-4">
            <h4 class="page-title" style="color: black;">Products Variants</h4>
        </div>
        <div class="col-sm-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Product Variants</li>
                </ol>
            </nav>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="mb-3 d-flex justify-content-between">
        <form action="{{ route('admin.productVariants.index') }}" method="GET" class="d-flex">
            <select name="product_id" class="form-select me-2" onchange="this.form.submit()">
                <option value="">Tất cả sản phẩm</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ ($filters['product_id'] ?? '') == $product->id ? 'selected' : '' }}>
                        {{ $product->name }}
                    </option>
                @endforeach
            </select>

            <select name="category_id" class="form-select me-2" onchange="this.form.submit()">
                <option value="">Tất cả danh mục</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ ($filters['category_id'] ?? '') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            <select name="sort" class="form-select me-2" onchange="this.form.submit()">
                <option value="">Sắp xếp</option>
                <option value="id_asc" {{ ($filters['sort'] ?? '') == 'id_asc' ? 'selected' : '' }}>ID ↑</option>
                <option value="id_desc" {{ ($filters['sort'] ?? '') == 'id_desc' ? 'selected' : '' }}>ID ↓</option>
                <option value="price_asc" {{ ($filters['sort'] ?? '') == 'price_asc' ? 'selected' : '' }}>Giá ↑</option>
                <option value="price_desc" {{ ($filters['sort'] ?? '') == 'price_desc' ? 'selected' : '' }}>Giá ↓</option>
            </select>

            <input type="text" name="search" class="form-control me-2" placeholder="Tìm kiếm..." value="{{ $filters['search'] ?? '' }}">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>


        <a href="{{ route('admin.productVariants.create') }}" class="btn btn-success">Thêm biến thể</a>
    </div>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Sản phẩm</th>
                <th>Danh mục</th>
                <th>Thương hiệu</th>
                <th>Attribute</th>
                <th>Giá</th>
                <th>Số lượng</th>
                <th>Ảnh</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse($variants as $variant)
                <tr>
                    <td>{{ $variant->id }}</td>
                    <td>{{ $variant->product->name ?? '' }}</td>
                    <td>{{ $variant->product->category->name ?? '' }}</td>
                    <td>{{ $variant->brand }}</td>
                    <td>{{ $variant->attribute }}</td>
                    <td>{{ number_format($variant->price,0,',','.') }}₫</td>
                    <td>{{ $variant->quantity }}</td>
                    <td>
                        @if($variant->image)
                            <img src="{{ asset('storage/products/'.$variant->image) }}" width="60" alt="image">
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.productVariants.edit', $variant) }}" class="btn btn-warning btn-sm">Sửa</a>
                        <form action="{{ route('admin.productVariants.destroy', $variant) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Xóa</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="9">Không có biến thể sản phẩm</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $variants->links() }}
</div>
@endsection
