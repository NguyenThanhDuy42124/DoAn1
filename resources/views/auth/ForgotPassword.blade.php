@extends('layouts.app')
@section('title', 'Quên mật khẩu')
@section('content')
<style>
    /* === CSS DÙNG CHUNG (COPY TỪ LOGIN/REGISTER) === */
    .auth-wrapper {
        background-color: #f0f2f5;
        min-height: 85vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 0;
    }
    .auth-card-modern {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        width: 100%;
        max-width: 900px;
        display: flex;
        flex-wrap: wrap;
    }
    .auth-banner {
        flex: 1;
        min-width: 300px;
        background: linear-gradient(135deg, #0d6efd 0%, #0043a7 100%);
        color: white;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px;
        text-align: center;
        position: relative;
    }
    .auth-banner::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: url('https://www.transparenttextures.com/patterns/cubes.png');
        opacity: 0.1;
    }
    .auth-form-section {
        flex: 1.3;
        min-width: 350px;
        padding: 50px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .form-title { font-size: 1.8rem; font-weight: 700; color: #333; margin-bottom: 10px; text-align: center; }
    .form-subtitle { text-align: center; color: #666; margin-bottom: 30px; font-size: 0.95rem; }
    
    .custom-form-group { margin-bottom: 20px; }
    .custom-label { font-weight: 600; font-size: 0.9rem; color: #555; margin-bottom: 8px; display: block; }
    .custom-input { width: 100%; padding: 12px 15px; border: 1px solid #e1e1e1; border-radius: 10px; background-color: #f9f9f9; font-size: 0.95rem; transition: all 0.3s ease; }
    .custom-input:focus { background-color: #fff; border-color: #0d6efd; box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1); outline: none; }
    
    .btn-auth-modern { width: 100%; padding: 14px; background: linear-gradient(90deg, #0d6efd 0%, #0b5ed7 100%); color: white; border: none; border-radius: 10px; font-size: 1rem; font-weight: 700; text-transform: uppercase; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; margin-top: 10px; }
    .btn-auth-modern:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(13, 110, 253, 0.3); }
    
    .auth-back-link { text-align: center; margin-top: 25px; color: #666; }
    .auth-back-link a { color: #0d6efd; font-weight: 700; text-decoration: none; }

    @media (max-width: 768px) { .auth-banner { display: none; } .auth-form-section { padding: 30px 20px; } }
</style>

<div class="auth-wrapper">
    <div class="container d-flex justify-content-center">
        <div class="auth-card-modern">
            
            {{-- CỘT TRÁI: BANNER --}}
            <div class="auth-banner">
                <div style="font-size: 2rem; font-weight: 800; margin-bottom: 20px;">TEN SHOP</div>
                <div style="font-size: 5rem; opacity: 0.2; margin-bottom: 20px;">
                    <i class="fas fa-lock-open"></i>
                </div>
                <p style="font-size: 1.1rem; opacity: 0.9; line-height: 1.6; z-index: 2;">
                    Quên mật khẩu? Đừng lo lắng.<br>
                    Nhập email để lấy lại quyền truy cập ngay.
                </p>
            </div>

            {{-- CỘT PHẢI: FORM --}}
            <div class="auth-form-section">
                <h3 class="form-title">Khôi Phục Mật Khẩu</h3>
                <p class="form-subtitle">Chúng tôi sẽ gửi liên kết xác nhận đến email của bạn</p>

                {{-- Hiển thị thông báo --}}
                @if (session('message'))
                    <div class="alert alert-success text-center" role="alert">
                        {{ session('message') }}
                    </div>
                @endif

                <form action="{{ route('forgetPassword.link') }}" method="POST">
                    @csrf
                    <div class="custom-form-group">
                        <label for="email" class="custom-label">Địa chỉ Email</label>
                        <input type="email" class="custom-input" id="email" name="email" 
                               placeholder="Nhập email đã đăng ký" required autofocus>
                        @error('email')
                            <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn-auth-modern">
                        Gửi Liên Kết Khôi Phục
                    </button>
                </form>

                <div class="auth-back-link">
                    <a href="/login"><i class="fas fa-arrow-left me-1"></i> Quay lại Đăng nhập</a>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection