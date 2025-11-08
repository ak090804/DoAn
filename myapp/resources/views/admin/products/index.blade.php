@extends('admin.layouts.admin')

@section('title', 'Admin Products')

@section('content')
<div class="container mt-4">

    <div class="row mb-4">
        <div class="col-sm-4">
            <h4 class="page-title" style="color: black;">Products</h4>
        </div>
        <div class="col-sm-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Products</li>
                </ol>
            </nav>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="mb-3 d-flex justify-content-between">
        <form action="{{ route('admin.products.index') }}" method="GET" class="d-flex">
            <select name="category_id" class="form-select me-2" onchange="this.form.submit()">
                <option value="">Tất cả danh mục</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ ($filters['category_id'] ?? '') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            <input type="text" name="search" class="form-control me-2" placeholder="Tìm kiếm" value="{{ $filters['search'] ?? '' }}">
            <select name="sort" class="form-select me-2" onchange="this.form.submit()">
                <option value="">Sắp xếp</option>
                <option value="id_asc" {{ ($filters['sort'] ?? '') == 'id_asc' ? 'selected' : '' }}>ID ↑</option>
                <option value="id_desc" {{ ($filters['sort'] ?? '') == 'id_desc' ? 'selected' : '' }}>ID ↓</option>
                <option value="name_asc" {{ ($filters['sort'] ?? '') == 'name_asc' ? 'selected' : '' }}>Tên ↑</option>
                <option value="name_desc" {{ ($filters['sort'] ?? '') == 'name_desc' ? 'selected' : '' }}>Tên ↓</option>
            </select>
        </form>

        <a href="{{ route('admin.products.create') }}" class="btn btn-success">Thêm sản phẩm</a>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên</th>
                <th>Danh mục</th>
                <th>Variants</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category->name ?? '' }}</td>
                    <td>{{ $product->productVariants->count() }}</td>
                    <td>
                        <a href="{{ route('admin.productVariants.index', ['product_id' => $product->id]) }}" class="btn btn-success btn-sm">Xem</a>
                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-warning btn-sm">Sửa</a>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Xóa</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">Không có sản phẩm</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $products->links() }}
</div>
@endsection
