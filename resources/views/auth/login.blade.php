@extends('layouts.app')
@section('title', 'Đăng nhập')
@section('content')


<div class="login-wrapper">
    <div class="container d-flex justify-content-center">
        
        <div class="login-card-modern">
            
            {{-- CỘT TRÁI: BANNER --}}
            <div class="login-banner">
                <div class="store-logo-large">DDK Mobile Market</div>
                <p class="banner-text">
                    Chào mừng bạn quay trở lại.<br>
                    Đăng nhập để tiếp tục mua sắm và quản lý đơn hàng.
                </p>
                {{-- Ảnh minh họa login --}}
                <div style="font-size: 5rem; opacity: 0.2; margin-top: 20px;">
                    <i class="fas fa-user-circle"></i>
                </div>
            </div>

            {{-- CỘT PHẢI: FORM ĐĂNG NHẬP --}}
            <div class="login-form-section">
                <h3 class="form-title">Đăng Nhập</h3>
                <p class="form-subtitle">Vui lòng nhập thông tin tài khoản của bạn</p>

                {{-- Hiển thị thông báo lỗi (Logic cũ) --}}
                @if($errors->has('login'))
                    <div class="alert alert-danger text-center py-2" role="alert">
                        <i class="fas fa-exclamation-circle me-1"></i> {{ $errors->first('login') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger text-center py-2" role="alert">
                        <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
                    </div>
                @endif

                <form action="/login" method="POST">
                    @csrf
                    
                    {{-- Email --}}
                    <div class="custom-form-group">
                        <label for="email" class="custom-label">Email</label>
                        <input type="email" class="custom-input" id="email" name="email"
                            placeholder="Nhập email của bạn" required autofocus>
                    </div>

                    {{-- Password --}}
                    <div class="custom-form-group">
                        <label for="password" class="custom-label">Mật Khẩu</label>
                        <input type="password" class="custom-input" id="password" name="password"
                            placeholder="Nhập mật khẩu" required>
                        
                        {{-- Link quên mật khẩu (Logic cũ) --}}
                        <a href="{{ route('forgetPassword.form') }}" class="forgot-password-link">
                            Quên mật khẩu?
                        </a>
                    </div>

                    <button type="submit" class="btn-login-modern">Đăng Nhập</button>
                </form>

                <div class="modern-divider">
                    <span>Hoặc đăng nhập bằng</span>
                </div>

                {{-- Social Login (Chỉ có Google theo code cũ) --}}
                <div class="social-buttons">
                    <a href="{{ route('google.auth.redirect') }}" class="btn-social-modern">
                        <i class="fab fa-google"></i> Google
                    </a>
                </div>

                <div class="register-redirect">
                    Chưa có tài khoản? <a href="/register">Đăng ký ngay</a>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection