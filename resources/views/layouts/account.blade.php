@extends('layouts.app')

@section('content')
<div class="container account-container">

    <div class="card account-header mb-4">
        @if(Auth::user()->img == "")
            <img src="{{ asset('storage/profile_images/default.jpg') }}" alt="none default Image" class="account-avatar">
        @else
            <img src="{{ asset('storage/' . Auth::user()->img) }}" alt="Profile Image" class="account-avatar">
        @endif
        
        <div>
            <h3>{{ Auth::user()->name }}</h3>
            <p>Thành viên từ: {{ Auth::user()->created_at->format('d/m/Y') }}</p>
        </div>
    </div>

    <div class="row">

        <div class="col-lg-4">
            <div class="card account-card account-sidebar">
                <div class="nav flex-column nav-pills p-3" role="tablist" aria-orientation="vertical">

                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
                       href="{{ route('dashboard') }}">
                        <i class="fas fa-user-circle"></i> Thông tin cá nhân
                    </a>

                    <a class="nav-link {{ request()->routeIs('buyer.orders.index') ? 'active' : '' }}" 
                       href="{{ route('buyer.orders.index') }}">
                        <i class="fas fa-shopping-bag"></i> Đơn hàng
                    </a>

                    <a class="nav-link" href="#"> {{-- Ví dụ: href="{{ route('wishlist.index') }}" --}}
                        <i class="fas fa-heart"></i> Sản phẩm yêu thích
                    </a>
                    <a class="nav-link {{ request()->routeIs('buyer.carts.index') ? 'active' : '' }}" 
                       href="{{ route('buyer.carts.index') }}">
                        <i class="fas fa-shopping-cart"></i> Giỏ hàng
                    </a>
                    <a class="nav-link {{ request()->routeIs('notifications.index') ? 'active' : '' }}" 
                       href="{{ route('notifications.index') }}">
                        <i class="fas fa-bell"></i> Thông báo
                    </a>

                    <a class="nav-link" href="#"> {{-- Ví dụ: href="{{ route('security.change_password') }}" --}}
                        <i class="fas fa-shield-alt"></i> Bảo mật
                    </a>
                    
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card account-card account-content">
                
                @yield('account_content') {{-- Đây là nơi nạp nội dung của các trang con --}}

            </div>
        </div>

    </div>
</div>

{{-- Giữ lại nút Chat nếu có (lấy từ file dashboard cũ) --}}
@if(config('chatify.pusher.key') != null)
    <button
    type="button"
    id="chat-bubble"
    class="btn btn-primary shadow rounded-circle"
    onclick="window.location.href='{{ route('chatify') }}'">
    <i class="bi bi-chat-dots-fill"></i>
    </button>
@endif

@endsection

@push('styles')
{{-- Nạp CSS riêng nếu bạn không muốn gộp chung vào layouts.css --}}
{{-- <link rel="stylesheet" href="{{ asset('css/account_dashboard.css') }}"> --}}

{{-- Thêm icon cho nút chat nếu layouts.app chưa có --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endpush