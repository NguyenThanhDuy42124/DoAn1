<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
                    <h2>Tạo Tài Khoản</h2>
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
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>
</body>

</html>
