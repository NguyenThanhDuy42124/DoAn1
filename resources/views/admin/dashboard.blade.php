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
                    <a href="#" class="active">
                        <i class="fas fa-tachometer-alt"></i>
                        Tổng quan
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-users"></i>
                        Quản lý người dùng
                    </a>
                </li>
                <li>
                    <a href="#">
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

            <!-- Main Content -->
            <div class="container-fluid">
                <!-- Stats Row -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stats-card" style="background: linear-gradient(to right, var(--primary), #004d99);">
                            <i class="fas fa-users"></i>
                            <div class="stats-value">{{ $users->count() }}</div>
                            <div class="stats-label">Người dùng</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card" style="background: linear-gradient(to right, var(--success), #1e7e34);">
                            <i class="fas fa-shopping-cart"></i>
                            <div class="stats-value">10</div>
                            <div class="stats-label">Đơn hàng</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card" style="background: linear-gradient(to right, var(--info), #138496);">
                            <i class="fas fa-box"></i>
                            <div class="stats-value">50</div>
                            <div class="stats-label">Sản phẩm</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card" style="background: linear-gradient(to right, var(--warning), #d39e00);">
                            <i class="fas fa-money-bill-wave"></i>
                            <div class="stats-value">10.3 triệu</div>
                            <div class="stats-label">Doanh thu</div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="dashboard-card">
                            <div class="card-header">Doanh thu theo tháng</div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="revenueChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="dashboard-card">
                            <div class="card-header">Phân loại người dùng</div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="userTypeChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Management & Notifications Row -->
                <!-- Thêm phần quản lý người dùng và gửi thông báo ở đây -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="dashboard-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <!-- Search và Add Buttons -->
                                <span>Quản lý người dùng</span>
                                <div>
                                    <button class="btn btn-sm btn-secondary" data-toggle="collapse"
                                        data-target="#searchPanel">
                                        <i class="fas fa-search"></i> Tìm kiếm
                                    </button>
                                    <a class="btn btn-sm btn-primary" href="{{ route('users.create') }}">
                                        <i class="fas fa-plus"></i> Thêm mới
                                    </a>
                                </div>
                            </div>

                            <!-- Collapse search form -->
                            <div class="collapse mt-2" id="searchPanel" style="">
                                <form method="GET" action="{{ route('admin.dashboard') }}" class="form-inline mb-3"
                                    style="align-items: left;">
                                    <div class="form-group mr-2">
                                        <input type="text" name="keyword" value="{{ request('keyword') }}"
                                            class="form-control form-control-sm" placeholder="Nhập tên người dùng"
                                            style="height: 55px;">
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="fas fa-filter"></i> Lọc
                                    </button>
                                </form>
                            </div>

                            <!-- User Table -->
                            @if (session()->has('message'))
                            <h3 style="align-self: center">{{ session('message') }}</h3>
                            @endif
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Tên người dùng</th>
                                                <th>Email</th>
                                                <th>Vai trò</th>
                                                <th>Trạng thái</th>
                                                <th>Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($users as $user)
                                            <tr>
                                                <td>{{ $user->id }}</td>
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>
                                                    {{ $user->role }}
                                                    <!-- cái này nữa làm cái form select để đổi role
                                                        form này sẽ xuất hiện khi bấm nút edit ( edit sẽ thay đổi dc tên email vs role)
                                                        <select class="form-control" style="height: 50px;">
                                                        <option value="Buyer" {{ $user->role == 'buyer' ? 'selected' : '' }}>Buyer</option>
                                                        <option value="Seller" {{ $user->role == 'seller' ? 'selected' : '' }}>Seller</option>
                                                        <option value="Admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                                    </select>-->
                                                </td>
                                                <td><span class="badge badge-success">Active</span></td>
                                                <td>
                                                    <a class="btn btn-sm btn-info"
                                                        href="{{ route('users.edit', $user->id) }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                                        style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-sm btn-danger"><i
                                                                class="fas fa-trash"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                    <!-- Pagination -->
                                    {{ $users->links() }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="dashboard-card">
                            <div class="card-header">Gửi thông báo</div>
                            <div class="card-body">
                                <form>
                                    <div class="form-group">
                                        <label>Tiêu đề thông báo</label>
                                        <input type="text" class="form-control" placeholder="Nhập tiêu đề">
                                    </div>
                                    <div class="form-group">
                                        <label>Nội dung thông báo</label>
                                        <textarea class="form-control" rows="3"
                                            placeholder="Nhập nội dung thông báo"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Gửi đến</label>
                                        <select class="form-control" style="height: 50px;">
                                            <option>Tất cả người dùng</option>
                                            <option>Người dùng thường</option>
                                            <option>Người bán hàng</option>
                                            <option>Quản trị viên</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-block">Gửi thông báo</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
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

</body>

</html>
