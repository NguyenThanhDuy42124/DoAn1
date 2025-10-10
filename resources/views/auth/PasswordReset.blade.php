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
                    <form action="{{ route('password.update') }}" method="POST">
                        @csrf
                        <div>
                            <input type="hidden" name="token" value="{{ $token }}">

                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="nhapemail@domain.com"
                                value="{{ request('email') }}" autocomplete="email" required>
                            @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <label class="form-label">Mật khẩu mới</label>
                            <input type="password" name="password" class="form-control" autocomplete="new-password"
                                minlength="8" required>
                            @error('password') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <label class="form-label">Nhập lại mật khẩu mới</label>
                            <input type="password" name="password_confirmation" class="form-control"
                                autocomplete="new-password" minlength="8" required>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Đặt lại mật khẩu</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endsection
 
</body>

</html>
