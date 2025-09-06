<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Đăng nhập</title>
    <style>
        :root {
            --primary: #0066cc;
            --secondary: #ff6600;
            --light-bg: #f8f9fa;
            --dark-text: #333;
        }
        
        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: linear-gradient(to bottom, #f5f7fb 0%, #e8ecf1 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 20px 0;
        }
        
        .login-container {
            max-width: 450px;
            width: 100%;
            margin: 0 auto;
        }
        
        .login-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .login-header {
            background: var(--primary);
            color: white;
            padding: 25px;
            text-align: center;
            position: relative;
        }
        
        .login-header h1 {
            margin: 0;
            font-weight: 600;
            font-size: 28px;
        }
        
        .login-header:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 100%;
            height: 20px;
            background: white;
            border-radius: 20px 20px 0 0;
        }
        
        .login-body {
            padding: 30px;
        }
        
        .store-logo {
            font-size: 24px;
            font-weight: bold;
            color: white;
            margin-bottom: 15px;
            display: inline-block;
            background: var(--secondary);
            padding: 5px 15px;
            border-radius: 5px;
        }
        
        .form-group label {
            font-weight: 500;
            color: var(--dark-text);
            margin-bottom: 8px;
        }
        
        .form-control {
            border: 1px solid #dce1e6;
            border-radius: 6px;
            padding: 12px 15px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(0, 102, 204, 0.25);
        }
        
        .btn-login {
            background: var(--secondary);
            border: none;
            color: white;
            padding: 12px;
            font-weight: 600;
            border-radius: 6px;
            width: 100%;
            transition: all 0.3s;
            margin-top: 10px;
        }
        
        .btn-login:hover {
            background: #e55a00;
            transform: translateY(-2px);
        }
        
        .register-link {
            text-align: center;
            margin-top: 20px;
            color: #6c757d;
        }
        
        .register-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }
        
        .divider {
            display: flex;
            align-items: center;
            margin: 20px 0;
        }
        
        .divider::before, .divider::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid #dee2e6;
        }
        
        .divider span {
            padding: 0 10px;
            color: #6c757d;
            font-size: 14px;
        }
        
        .social-login {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 15px;
        }
        
        .social-btn {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
            transition: all 0.3s;
        }
        
        .social-btn:hover {
            transform: translateY(-3px);
        }
        
        .fb-btn {
            background: #3b5998;
        }
        
        .google-btn {
            background: #dd4b39;
        }
        
        .alert-danger {
            border-radius: 6px;
            margin-top: 15px;
            padding: 10px 15px;
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        
        .forgot-password {
            text-align: right;
            margin-top: 10px;
        }
        
        .forgot-password a {
            color: var(--primary);
            text-decoration: none;
            font-size: 14px;
        }
        
        .features {
            display: flex;
            justify-content: space-between;
            margin-top: 25px;
            text-align: center;
        }
        
        .feature-item {
            flex: 1;
            padding: 0 10px;
        }
        
        .feature-icon {
            font-size: 24px;
            color: var(--primary);
            margin-bottom: 8px;
        }
        
        .feature-text {
            font-size: 13px;
            color: #6c757d;
        }
    </style>
</head>
<body>
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
                            <label for="name">Họ và Tên</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Nhập họ và tên" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Mật Khẩu</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Nhập mật khẩu" required>
                            <div class="forgot-password">
                                <a href="/forgot-password">Quên mật khẩu?</a>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-login">Đăng Nhập</button>
                        
                        {{-- Hiển thị lỗi đăng nhập --}}
                        @if($errors->has('login'))
                        <div class="alert alert-danger">{{ $errors->first('login') }}</div>
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

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>