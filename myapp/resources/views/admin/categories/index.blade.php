@extends('admin.layouts.admin')

@section('title', 'Admin Categories')

@section('content')

<div class="container mt-4">

    <!-- Breadcrumbs -->
    <div class="row mb-4">
        <div class="col-sm-4">
            <h4 class="page-title" style="color: black;">Danh Mục</h4>
        </div>
        <div class="col-sm-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Bảng Điều Khiển</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Danh Mục</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="mb-3 d-flex justify-content-between">
        @php
            $adminUserId = session('admin_user_id');
            $role = 'admin';
            if ($adminUserId) {
                $adminUser = \App\Models\User::find($adminUserId);
                $role = $adminUser->role ?? 'admin';
            }
        @endphp
        @if($role === 'admin')
            <a href="{{ route('admin.categories.create') }}" class="btn btn-success">Thêm danh mục</a>
        @endif
    </div>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên danh mục</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $category)
                <tr>
                    <td>{{ $category->id }}</td>
                    <td>{{ $category->name }}</td>
                    <td>
                        <a href="{{ route('admin.products.index', ['category_id' => $category->id]) }}" class="btn btn-success btn-sm">Xem</a>
                        @if($role === 'admin')
                            <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-warning btn-sm btn-edit">Sửa</a>
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm btn-delete">Xóa</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">Không có danh mục</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $categories->links() }}

</div>
@endsection
