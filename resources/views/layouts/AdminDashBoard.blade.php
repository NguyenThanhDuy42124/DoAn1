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
                        Quản lý banner
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
                        Quản lý cửa hàng
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
                    <a href="{{ route('admin.wallet') }}">
                        <i class="fas fa-wallet"></i>
                        Quản lý ví hệ thống
                    </a> 
                </li>
                <li>
                    <a href="{{ route('admin.notifications.index') }}">
                        <i class="fas fa-bell"></i>
                        Quản lý thông báo
                    </a>
                </li>
                <li> 
                   <!-- <a href="#">
                        <i class="fas fa-cog"></i>
                        Cài đặt hệ thống
                    </a> -->
                </li>
                <li>
                    <!--
                    <a href="#">
                        <i class="fas fa-chart-bar"></i>
                        Báo cáo & Thống kê
                    </a> -->
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
    <div class="dropdown mr-3">
        <button class="btn btn-light dropdown-toggle" type="button" id="notificationDropdown"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-bell"></i>
            {{-- Lấy số thông báo chưa đọc động --}}
            @php
                $unreadCount = \App\Models\Notification::where('user_id', auth()->id())
                    ->where('is_read', false)
                    ->count();
            @endphp
            @if ($unreadCount > 0)
                <span class="badge badge-danger">{{ $unreadCount }}</span>
            @endif
        </button>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="notificationDropdown"
            style="min-width: 300px;">
            <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                <h6 class="mb-0">Thông báo</h6>
                {{-- Nút đánh dấu tất cả đã đọc --}}
                @if ($unreadCount > 0)
                    <form action="{{ route('notifications.markAllAsRead') }}" method="POST"
                        class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-primary py-0"><small>Đánh
                                dấu tất cả đã đọc</small></button>
                    </form>
                @endif
            </div>
            {{-- Lấy 5 thông báo mới nhất --}}
            @php
                $notifications = \App\Models\Notification::where('user_id', auth()->id())
                    ->latest()
                    ->take(5)
                    ->get();
            @endphp
            <div class="dropdown-notifications">
                @forelse($notifications as $notification)
                    <div class="dropdown-item {{ $notification->is_read ? '' : 'bg-light' }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <a href="{{ route('notifications.markAsRead', $notification->id) }}"
                                    class="text-decoration-none text-dark {{ $notification->is_read ? '' : 'font-weight-bold' }}">
                                    <small
                                        class="d-block">{{ Str::limit($notification->message, 60) }}</small>
                                </a>
                                <small
                                    class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                            {{-- Nút đánh dấu 1 thông báo đã đọc --}}
                            @if (!$notification->is_read)
                                <form
                                    action="{{ route('notifications.markAsRead', $notification->id) }}"
                                    method="POST" class="ml-2">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success py-0"
                                        title="Đánh dấu đã đọc">
                                        <small><i class="fas fa-check"></i></small>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                    @if (!$loop->last)
                        <div class="dropdown-divider m-0"></div>
                    @endif
                @empty
                    <div class="dropdown-item text-center py-3">
                        <small class="text-muted">Không có thông báo</small>
                    </div>
                @endforelse
            </div>
            <div class="dropdown-divider m-0"></div>
            <a class="dropdown-item text-center py-2" href="{{ route('notifications.index') }}">
                <small class="text-primary">Xem tất cả thông báo</small>
            </a>
        </div>
    </div>

    <div class="dropdown">
        <button class="btn btn-light dropdown-toggle d-flex align-items-center" type="button"
            id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            {{-- Lấy Avatar và Tên động --}}
            @if (Auth::user()->img == '')
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=ff6600&color=fff"
                    width="30" height="30" class="rounded-circle mr-2">
            @else
                <img src="{{ asset('storage/' . Auth::user()->img) }}" width="30" height="30"
                    class="rounded-circle mr-2">
            @endif
            {{ Auth::user()->name }}
        </button>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
            {{-- Menu đã bỏ "Cài đặt" để giống Seller --}}
            <a class="dropdown-item" href="/dashboard"><i class="fas fa-user-circle mr-2"></i> Hồ sơ</a>

            <div class="dropdown-divider"></div>
            <form id="logout-form" action="/logout" method="POST" style="display: none;">@csrf
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
