<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
