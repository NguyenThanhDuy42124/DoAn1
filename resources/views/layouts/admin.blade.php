<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    @vite(['resources/css/dashboard_admin.css', 'resources/js/app.js'])
</head>
<body>
    <div class="wrapper d-flex">
        {{-- Sidebar --}}
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3>tenshop Admin</h3>
            </div>
            <ul class="list-unstyled components">
                <li class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i> Tổng quan
                    </a>
                </li>
                <li class="{{ request()->is('admin/users*') ? 'active' : '' }}">
                    <a href="{{ route('users.index') }}">
                        <i class="fas fa-users"></i> Quản lý người dùng
                    </a>
                </li>
                <li class="{{ request()->is('admin/products*') ? 'active' : '' }}">
                    <a href="{{ route('admin.product.index') }}">
                        <i class="fas fa-box"></i> Quản lý sản phẩm
                    </a>
                </li>
                {{-- mấy menu khác --}}
            </ul>
        </nav>

        {{-- Nội dung chính --}}
        <div id="content" class="p-4 w-100">
            @yield('content')
        </div>
    </div>
</body>
</html>