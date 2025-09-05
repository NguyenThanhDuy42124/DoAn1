<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <title>Document</title>
</head>
<body>

    @auth
    <p>trang chủ sau khi đăng nhập</p>
    <form action="/logout" method="POST">
            @csrf
            <button type="submit" class="btn btn-danger">Đăng Xuất</button>
        </form>
    

    @else

    <div class="border p-3 mb-2 bg-dark text-white">
        <h2>Tạo Tài Khoản</h2>
        <form action="/register" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Họ và Tên</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Mật Khẩu</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Đăng Ký</button>
        </form>
    </div>
    <div class="border p-3 bg-dark text-white">
        <h1>Đăng Nhập</h1>

        <form action="/login" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Họ và Tên</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="password">Mật Khẩu</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Đăng Nhập</button>
            {{-- Hiển thị lỗi đăng nhập --}}
            @if($errors->has('login'))
            <div class="alert alert-danger">{{ $errors->first('login') }}</div>
            @endif
        </form>
    </div>
    @endauth




</body>
</html>
