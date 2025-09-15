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
    <title>Dashboard Admin - TechStore</title>
</head>

<body style="padding-top: 0">
    <div class="wrapper pt-0">
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3>tenshop Admin</h3>
            </div>

            <ul class="list-unstyled components">
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="active">
                        <i class="fas fa-tachometer-alt"></i>
                        Tổng quan
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.users.manager') }}">
                        <i class="fas fa-users"></i>
                        Quản lý người dùng
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.categories.index') }}">
                        <i class="fas fa-box"></i>
                        Quản lý sản phẩm
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-shopping-cart"></i>
                        Quản lý đơn hàng
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-bell"></i>
                        Gửi thông báo
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
                        <i class="fas fa-user-shield"></i>
                        Phân quyền
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
                    <a href="/" class="btn btn-light btn-block">
                        <i class="fas fa-home"></i> Về trang chủ
                    </a>
                </li>
                <li>
                    <form action="/logout" method="POST">
                        <button type="submit" class="btn btn-light btn-block hover-green">
                            <i class="fas fa-sign-out-alt"></i> Đăng xuất
                        </button>
                    </form>
                </li>
            </ul>
        </nav>

        <!-- Content -->
        <div id="content">
            <!-- Header -->
            <nav class="navbar navbar-expand-lg">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-primary">
                        <i class="fas fa-bars"></i>
                        <span>Menu</span>
                    </button>

                    <div class="ml-auto d-flex align-items-center">
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

                        <div class="dropdown">
                            <button class="btn btn-light dropdown-toggle" type="button" id="userDropdown"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img src="https://ui-avatars.com/api/?name=Admin+User&background=ff6600&color=fff"
                                    width="30" height="30" class="rounded-circle mr-2">
                                Admin User
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user-circle mr-2"></i> Hồ sơ
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-cog mr-2"></i> Cài đặt
                                </a>
                                <div class="dropdown-divider"></div>
                                <!-- Logout form lưu ý phần này vì có thể tái xử dung -->
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
                </div>
            </nav>
        </div>
    </div>

            <!-- Main Content -->
            @yield('content')



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

</body>

</html>
