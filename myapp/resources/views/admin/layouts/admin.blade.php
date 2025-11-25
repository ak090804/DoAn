<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Panel - @yield('title')</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>

<div class="d-flex" id="wrapper">

    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <div class="sidebar-heading">
            <i class="fas fa-cog me-2"></i>Admin Panel
        </div>
        <div class="list-group list-group-flush">
            <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action">
                <i class="fas fa-chart-line me-2"></i>Bảng Điều Khiển
            </a>
            @php
                $adminUserId = session('admin_user_id');
                if ($adminUserId) {
                    $adminUser = \App\Models\User::find($adminUserId);
                    $role = $adminUser->role ?? 'admin';
                    $adminName = $adminUser->name ?? 'Admin';
                } else {
                    $role = auth()->user()->role ?? 'admin';
                    $adminName = auth()->user()->name ?? 'Admin';
                }
            @endphp

            @if(in_array($role, ['admin','staff','inventory']))
                <a href="{{ route('admin.categories.index') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-list me-2"></i>Danh Mục
                </a>
                <a href="{{ route('admin.products.index') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-box me-2"></i>Sản Phẩm
                </a>
                <a href="{{ route('admin.productVariants.index') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-cube me-2"></i>Biến Thể
                </a>
                <a href="{{ route('admin.customers.index') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-users me-2"></i>Khách Hàng
                </a>
            @endif

            @if($role === 'admin')
                <a href="{{ route('admin.users.index') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-user-tie me-2"></i>Người Dùng
                </a>
                <a href="{{ route('admin.employees.index') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-user-clock me-2"></i>Nhân Viên
                </a>
                <a href="{{ route('admin.suppliers.index') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-truck me-2"></i>Nhà Cung Cấp
                </a>
                <a href="{{ route('admin.orders.index') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-shopping-cart me-2"></i>Đơn Hàng
                </a>
                <a href="{{ route('admin.import-notes.index') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-file-import me-2"></i>Phiếu Nhập
                </a>
            @elseif($role === 'staff')
                {{-- staff (thủ ngân): can view Orders but no create/edit/delete --}}
                <a href="{{ route('admin.orders.index') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-shopping-cart me-2"></i>Đơn Hàng
                </a>
            @elseif($role === 'inventory')
                {{-- inventory (kiểm kho): can view Import Notes but no create/edit/delete --}}
                <a href="{{ route('admin.import-notes.index') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-file-import me-2"></i>Phiếu Nhập
                </a>
            @endif
        </div>
    </div>
    <!-- /#sidebar-wrapper -->

    <!-- Page Content -->
    <div id="page-content-wrapper">

        <!-- Top Navigation -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom shadow-sm">
            <div class="container-fluid">
                <button class="btn btn-outline-primary btn-sm" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <span class="navbar-brand ms-2">
                    <strong>Hệ Thống Quản Lý Admin</strong>
                </span>
                <div class="ms-auto d-flex align-items-center gap-3">
                    <span class="text-muted">
                        <i class="fas fa-user me-1"></i>{{ $adminName ?? (auth()->user()->name ?? 'Admin') }}
                    </span>
                    <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="fas fa-sign-out-alt me-1"></i>Đăng Xuất
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main id="full-content" class="container-fluid py-4">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-light py-3 mt-5 text-center border-top">
            <p class="mb-0">&copy; {{ date('Y') }} Hệ Thống Quản Lý Admin. Bản Quyền Được Bảo Vệ.</p>
        </footer>

    </div>
    <!-- /#page-content-wrapper -->

</div>
<!-- /#wrapper -->

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Toggle sidebar
    document.getElementById('sidebarToggle').addEventListener('click', function () {
        document.getElementById('wrapper').classList.toggle('toggled');
    });
</script>
</body>
</html>

@yield('scripts')

</body>
</html>
