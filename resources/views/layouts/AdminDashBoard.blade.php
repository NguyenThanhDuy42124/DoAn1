<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    @vite(['resources/css/dashboard_admin.css', 'resources/js/app.js'])
    @livewireStyles <title>Dashboard Admin - TechStore</title>
</head>

<body class="admin-dashboard">
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar">


            <ul class="list-unstyled components">
                <li>
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i>
                        Tổng quan
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.homepage.images.manager') }}">
                        <i class="fas fa-user-shield"></i>
                        Quản lý Ảnh ở trang chủ
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.users.manager') }}">
                        <i class="fas fa-users"></i>
                        Quản lý người dùng
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.products.index') }}">
                        <i class="fas fa-box"></i>
                        Quản lý sản phẩm
                    </a>
                </li>
                <li>
                    <a href="#storeSetupSubmenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="fas fa-store"></i>
                        Quản lý Cửa hàng
                    </a>
                    <ul class="collapse list-unstyled" id="storeSetupSubmenu">
                        <li>
                            <a href="{{ route('admin.categories.manager') }}">Danh mục</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.attributes.manager') }}">Thuộc tính</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.brands.manager') }}">Thương hiệu</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-shopping-cart"></i>
                        Quản lý đơn hàng
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.notifications.index') }}">
                        <i class="fas fa-bell"></i>
                        Quản lý thông báo
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-cog"></i>
                        Cài đặt hệ thống
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-chart-bar"></i>
                        Báo cáo & Thống kê
                    </a>
                </li>
            </ul>

            <ul class="list-unstyled CTAs">
                <li>
                    <a href="/" class="btn btn-block text-white fw-bold">
                        <i class="fas fa-home me-2"></i>Về trang chủ
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Content -->
        <div id="content">
            <!-- Header -->
            <nav class="navbar navbar-expand-lg px-3">
                <button type="button" id="sidebarCollapse">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="ml-auto d-flex align-items-center">
                    <!-- Notification -->
                    <div class="dropdown mr-3">
                        <button class="btn btn-light dropdown-toggle" type="button" id="notificationDropdown"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-bell"></i>
                            <span class="badge badge-danger">3</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="notificationDropdown">
                            <h6 class="dropdown-header">Thông báo</h6>
                            <a class="dropdown-item" href="#">Đơn hàng mới #12345</a>
                            <a class="dropdown-item" href="#">Người dùng mới đăng ký</a>
                            <a class="dropdown-item" href="#">Sản phẩm sắp hết hàng</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-center" href="#">Xem tất cả</a>
                        </div>
                    </div>

                    <!-- User Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle d-flex align-items-center" type="button" id="userDropdown"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img src="https://ui-avatars.com/api/?name=Admin+User&background=ff6600&color=fff"
                                width="30" height="30" class="rounded-circle mr-2">
                            Admin User
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                            <a class="dropdown-item" href="/dashboard">
                                <i class="fas fa-user-circle mr-2"></i> Hồ sơ
                            </a>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-cog mr-2"></i> Cài đặt
                            </a>
                            <div class="dropdown-divider"></div>
                            <form id="logout-form" action="/logout" method="POST" style="display: none;">
                                @csrf
                            </form>
                            <a class="dropdown-item" href="#"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt mr-2"></i> Đăng xuất
                            </a>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <div class="container-fluid">
                @if (isset($slot))
                    {{ $slot }}
                @else
                    @yield('content')
                @endif
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
      // Sidebar toggle
$(document).ready(function () {
    $('#sidebarCollapse').on('click', function () {
        $('#sidebar').toggleClass('active');
        $('#content').toggleClass('sidebar-open'); // Thêm dòng này

        // Thêm/xóa overlay trên mobile
        if ($(window).width() < 992) {
            if ($('#sidebar').hasClass('active')) {
                $('<div class="overlay active"></div>').appendTo('body');
            } else {
                $('.overlay').remove();
            }
        }
    });

    // Đóng sidebar khi click overlay
    $(document).on('click', '.overlay', function() {
        $('#sidebar').removeClass('active');
        $('#content').removeClass('sidebar-open'); // Thêm dòng này
        $('.overlay').remove();
    });
});
    </script>
    @livewireScripts

    @stack('scripts')
</body>

</html>
