<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    @vite(['resources/css/login.css', 'resources/js/app.js'])
    <title>Quên mật khẩu</title>
</head>

<body>
    @extends('layouts.app')
    @section('content')
    <div class="container">
        <div class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <div class="store-logo">ten shop</div>
                    <h1>Đổi Mật Khẩu</h1>
                </div>
                <div class="login-body">
                    <form action="{{ route('forgetPassword.link') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Nhập email"
                                required>
                        </div>
                        <button type="submit" class="btn btn-primary">Gửi liên kết đặt lại mật khẩu</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endsection
   
</body>

</html>
