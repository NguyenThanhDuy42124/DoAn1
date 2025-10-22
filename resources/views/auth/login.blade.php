<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    @vite(['resources/css/login.css', 'resources/js/app.js'])
    <title>Đăng nhập</title>
</head>

<body>
    @extends('layouts.app')
    @section('content')
    <div class="container">
        <div class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <div class="store-logo">ten shop</div>
                    <h1>Đăng Nhập</h1>
                </div>
                <div class="login-body">
                    <form action="/login" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Nhập email"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="password">Mật Khẩu</label>
                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="Nhập mật khẩu" required>
                            <div class="forgot-password">
                                <a href="{{ route('forgetPassword.form') }}">Quên mật khẩu?</a>
                                </div>
                            </div>
                        <button type="submit" class="btn btn-login">Đăng Nhập</button>

                        {{-- Hiển thị lỗi đăng nhập --}}

                        @if($errors->has('login'))
                        <div class="alert alert-danger">{{ $errors->first('login') }}</div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif
                    </form>

                    <div class="divider">
                        <span>Hoặc đăng nhập bằng</span>
                    </div>

                    <div class="social-login">
                        <a href="#" class="social-btn fb-btn">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-btn google-btn">
                            <i class="fab fa-google"></i>
                        </a>
                    </div>

                    <div class="register-link">
                        Chưa có tài khoản? <a href="/register">Đăng ký ngay</a>
                    </div>

                    <div class="features">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-shipping-fast"></i>
                            </div>
                            <div class="feature-text">Giao hàng nhanh</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="feature-text">Bảo mật thông tin</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-headset"></i>
                            </div>
                            <div class="feature-text">Hỗ trợ 24/7</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection

</body>

</html>
