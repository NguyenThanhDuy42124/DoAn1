@extends('layouts.AdminDashBoard')
@section('content')
<div class="container">
    {{-- Sử dụng cấu trúc card giống như UserManager.blade.php --}}
    <div class="card shadow-sm">

        {{-- Card Header: Chứa tiêu đề trang --}}
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Quản lý sản phẩm</h4>
            <div>
                {{-- Bạn có thể thêm các nút chung (như Thêm mới) ở đây nếu cần --}}
                {{-- <a class="btn btn-sm btn-primary" href="#">
                    <i class="fas fa-plus"></i> Thêm mới
                </a> --}}
            </div>
        </div>

        {{-- Card Body: Chứa nội dung chính của trang --}}
        <div class="card-body">
            @if(session('message') || session('error'))
            <div class="mb-4 alert alert-info">
                {{-- Hiển thị thông báo thành công hoặc lỗi --}}

                <h5 class="alert-heading">{{ session('message') }}</h5>
                <h5 class="alert-heading">{{ session('error') }}</h5>

            </div>
            @endif
            {{--
              THAY ĐỔI: Chuyển từ "nav-tabs" sang "nav-pills" (kiểu viên thuốc)
              Thêm "nav-fill" để các tab có chiều rộng bằng nhau và lấp đầy không gian.
            --}}
            <ul class="nav nav-pills nav-fill mb-4">
                <li class="nav-item">
                    <a class="nav-link {{ (request('tab') == 'pending' || !request('tab')) ? 'active' : '' }}" href="{{ route('admin.products.index', ['tab' => 'pending']) }}">
                        Chờ duyệt {{-- Thêm count sau --}}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('tab') == 'all' ? 'active' : '' }}" href="{{ route('admin.products.index', ['tab' => 'all']) }}">
                        Tất cả
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('tab') == 'hidden-rejected' ? 'active' : '' }}" href="{{ route('admin.products.index', ['tab' => 'hidden-rejected']) }}">
                        Bị ẩn & Từ chối
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('tab') == 'categories' ? 'active' : '' }}" href="{{ route('admin.products.index', ['tab' => 'categories']) }}">
                        Danh mục
                    </a>
                </li>
            </ul>

            {{-- Tab content (Nội dung bảng Livewire của bạn) --}}
            <div class="tab-content">
                @if(request('tab') == 'pending' || !request('tab')) {{-- Default pending --}}
                @livewire('admin.products.pending')
                @elseif(request('tab') == 'all')
                @livewire('admin.products.all')
                @elseif(request('tab') == 'hidden-rejected')
                @livewire('admin.products.hidden-rejected')
                @elseif(request('tab') == 'categories')
                @livewire('admin.categories.manager')
                @endif
            </div>
        </div> {{-- End card-body --}}
    </div> {{-- End card --}}
</div> {{-- End container --}}
@endsection
