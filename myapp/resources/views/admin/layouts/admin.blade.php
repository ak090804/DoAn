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
            <a href="{{ route('admin.categories.index') }}" class="list-group-item list-group-item-action">Categories</a>
            <a href="{{ route('admin.products.index') }}" class="list-group-item list-group-item-action">Products</a>
            <a href="{{ route('admin.productVariants.index') }}" class="list-group-item list-group-item-action">Variants</a>
            <a href="{{ route('admin.users.index') }}" class="list-group-item list-group-item-action">Users</a>
                <a href="{{ route('admin.employees.index') }}" class="list-group-item list-group-item-action">Employees</a>
            <a href="{{ route('admin.customers.index') }}" class="list-group-item list-group-item-action">Customers</a>
            <a href="{{ route('admin.suppliers.index') }}" class="list-group-item list-group-item-action">Nhà cung cấp</a>
            <a href="{{ route('admin.orders.index') }}" class="list-group-item list-group-item-action">Orders</a>            
            <a href="{{ route('admin.import-notes.index') }}" class="list-group-item list-group-item-action">Phiếu nhập</a>
        </div>
    </div>
    <!-- /#sidebar-wrapper -->

    <!-- Page Content -->
    <div id="page-content-wrapper" class="w-100">

        <!-- Top Navigation -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
            <div class="container-fluid">
                <button class="btn btn-primary" id="sidebarToggle">Toggle Menu</button>
                <span class="ms-auto">Xin chào, {{ auth()->user()->name ?? 'Admin' }}</span>
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
