@extends('layouts.app')
@section('title', 'Đăng ký')
@section('content')

<div class="register-wrapper">
    <div class="container d-flex justify-content-center">
        
        <div class="register-card-modern">
            
            {{-- CỘT TRÁI: BANNER THƯƠNG HIỆU --}}
            <div class="register-banner">
            {{-- Thay ảnh minh họa ở đây --}}
            <img src="https://cdn-icons-png.flaticon.com/512/6183/6183594.png" alt="Register Illustration" width="250">
            <h2>Chào mừng bạn!</h2>
            <p>Đăng ký thành viên ngay để nhận những ưu đãi đặc biệt và trải nghiệm mua sắm tuyệt vời nhất.</p>
        </div>

            {{-- CỘT PHẢI: FORM ĐĂNG KÝ --}}
            <div class="register-form-section">
                <h3 class="form-title">Đăng Ký Tài Khoản</h3>

                <form action="/register" method="POST">
                    @csrf
                    
                    {{-- Hàng 1: Họ tên & SĐT --}}
                    <div class="form-row">
                        <div class="custom-form-group">
                            <label for="name" class="custom-label">Họ và Tên</label>
                            <input type="text" class="custom-input" id="name" name="name"
                                placeholder="VD: Nguyễn Văn A" required>
                        </div>
                        <div class="custom-form-group">
                            <label for="phoneNumber" class="custom-label">Số Điện Thoại</label>
                            <input type="text" class="custom-input" id="phoneNumber" name="phoneNumber"
                                placeholder="VD: 0912..." required>
                        </div>
                    </div>

                    {{-- Hàng 2: Ngày sinh & Giới tính --}}
                    <div class="form-row">
                        <div class="custom-form-group">
                            <label for="dateOfBirth" class="custom-label">Ngày Sinh</label>
                            <input type="date" class="custom-input" id="dateOfBirth" name="dateOfBirth" required>
                        </div>
                        <div class="custom-form-group">
                            <label for="gender" class="custom-label">Giới Tính</label>
                            <select class="custom-input" id="gender" name="gender" required>
                                <option value="">Chọn giới tính</option>
                                <option value="male">Nam</option>
                                <option value="female">Nữ</option>
                                <option value="other">Khác</option>
                            </select>
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="custom-form-group">
                        <label for="email" class="custom-label">Email</label>
                        <input type="email" class="custom-input" id="email" name="email"
                            placeholder="name@example.com" required>
                    </div>

                    {{-- Mật khẩu --}}
                    <div class="custom-form-group">
                        <label for="password" class="custom-label">Mật Khẩu</label>
                        <input type="password" class="custom-input" id="password" name="password"
                            placeholder="Nhập mật khẩu của bạn" required>
                    </div>

                    <button type="submit" class="btn-register-modern">Đăng Ký Ngay</button>
                </form>

                <div class="modern-divider">
                    <span>Hoặc đăng ký với</span>
                </div>

                <div class="social-buttons">
                    <a href="#" class="btn-social-modern fb-modern">
                        <i class="fab fa-facebook-f"></i> Facebook
                    </a>
                    <a href="#" class="btn-social-modern google-modern">
                        <i class="fab fa-google"></i> Google
                    </a>
                </div>

                <div class="login-redirect">
                    Đã có tài khoản? <a href="/login">Đăng nhập ngay</a>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection