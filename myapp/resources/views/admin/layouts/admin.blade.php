<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - @yield('title')</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>

<div class="d-flex" id="wrapper">

    <!-- Sidebar -->
    <div class="border-end bg-light" id="sidebar-wrapper">
        <div class="sidebar-heading text-center py-4 fw-bold">Admin Panel</div>
            <div class="list-group list-group-flush">
            <a href="#" class="list-group-item list-group-item-action">Dashboard</a>
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
                <a href="{{ route('admin.categories.index') }}" class="list-group-item list-group-item-action">Categories</a>
                <a href="{{ route('admin.products.index') }}" class="list-group-item list-group-item-action">Products</a>
                <a href="{{ route('admin.productVariants.index') }}" class="list-group-item list-group-item-action">Variants</a>
                <a href="{{ route('admin.customers.index') }}" class="list-group-item list-group-item-action">Customers</a>
            @endif

            @if($role === 'admin')
                <a href="{{ route('admin.users.index') }}" class="list-group-item list-group-item-action">Users</a>
                <a href="{{ route('admin.employees.index') }}" class="list-group-item list-group-item-action">Employees</a>
                <a href="{{ route('admin.suppliers.index') }}" class="list-group-item list-group-item-action">Nhà cung cấp</a>
                <a href="{{ route('admin.orders.index') }}" class="list-group-item list-group-item-action">Orders</a>
                <a href="{{ route('admin.import-notes.index') }}" class="list-group-item list-group-item-action">Phiếu nhập</a>
            @elseif($role === 'staff')
                {{-- staff (thu ngân): can view Orders but no create/edit/delete --}}
                <a href="{{ route('admin.orders.index') }}" class="list-group-item list-group-item-action">Orders</a>
            @elseif($role === 'inventory')
                {{-- inventory (kiểm kho): can view Import Notes but no create/edit/delete --}}
                <a href="{{ route('admin.import-notes.index') }}" class="list-group-item list-group-item-action">Phiếu nhập</a>
            @endif
        </div>
    </div>
    <!-- /#sidebar-wrapper -->

    <!-- Page Content -->
    <div id="page-content-wrapper" class="w-100">

        <!-- Top Navigation -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
            <div class="container-fluid">
                <button class="btn btn-primary" id="sidebarToggle">Toggle Menu</button>
                <div class="ms-auto d-flex align-items-center gap-3">
                    <span>Xin chào, {{ $adminName ?? (auth()->user()->name ?? 'Admin') }}</span>
                    <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-danger">Đăng xuất</button>
                    </form>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div id="full-content" class="mt-4">
            @yield('content')
        </div>


        <!-- Footer -->
        <footer class="bg-light py-3 mt-auto text-center border-top">
            &copy; {{ date('Y') }} Admin Panel. All rights reserved.
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

@yield('scripts')

</body>
</html>
