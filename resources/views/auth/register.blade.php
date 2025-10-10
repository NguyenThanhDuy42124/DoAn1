<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
  
    @vite(['resources/css/register.css', 'resources/js/app.js'])
    <title>Đăng ký</title>

</head>

<body>
    @extends('layouts.app')
    @section('content')
    <div class="container">
        <div class="register-container">
            <div class="register-card">
                <div class="register-header">
                    <div class="store-logo">ten shop</div>
                    <h3>Tạo Tài Khoản</h3>
                </div>
                <div class="register-body">
                    <form action="/register" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name">Họ và Tên</label>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="Nhập họ và tên đầy đủ" required>
                        </div>
                        <div class="form-group">
                            <label for="phoneNumber">Số Điện Thoại</label>
                            <input type="text" class="form-control" id="phoneNumber" name="phoneNumber"
                                placeholder="Nhập số điện thoại" required>
                        </div>
                        <div class="form-group">
                            <label for="dateOfBirth">Ngày Sinh</label>
                            <input type="date" class="form-control" id="dateOfBirth" name="dateOfBirth"
                                placeholder="Chọn ngày sinh" required>
                        </div>
                        <div class="form-group">
                            <label for="gender">Giới Tính</label>
                            <select class="form-control" id="gender" name="gender" required style="height: 50px;">
                                <option value="">Chọn giới tính</option>
                                <option value="male">Nam</option>
                                <option value="female">Nữ</option>
                                <option value="other">Khác</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                placeholder="Nhập địa chỉ email" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Mật Khẩu</label>
                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="Tạo mật khẩu mạnh" required>
                        </div>
                        <button type="submit" class="btn btn-register">Đăng Ký</button>
                    </form>

                    <div class="divider">
                        <span>Hoặc</span>
                    </div>

                    <div class="social-login">
                        <a href="#" class="social-btn fb-btn">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-btn google-btn">
                            <i class="fab fa-google"></i>
                        </a>
                    </div>

                    <div class="login-link">
                        Đã có tài khoản? <a href="/login">Đăng nhập ngay</a>
                    </div>


                </div>
            </div>
        </div>
    </div>
    @endsection
   
</body>

</html>
