<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <title>Document</title>
</head>

<body>
    <a href="/">Về trang chủ</a>
    <h1>Dashboard</h1>
    <p>Welcome to your dashboard!</p>
    <p>bạn tên là {{ Auth::user()->name }}</p>
    <ul>
        <li><a href="#">Profile</a></li>
        <li><a href="#">Settings</a></li>

        <form action="/logout" method="POST">
            @csrf
            <button type="submit" class="btn btn-danger">Đăng Xuất</button>
        </form>
    </ul>
    <!-- dashboard.blade.php -->
    <h1>Welcome, {{ Auth::user()->name }}</h1>

    <p>Bạn đang đăng nhập với vai trò: {{ session('current_role', Auth::user()->role) }}</p>

    @if(Auth::user()->role == 'seller')
    <a href="{{ route('switchRole', 'seller') }}" class="btn btn-primary">Seller</a>
    @endif

    @if(Auth::user()->role == 'admin')
    <a href="{{ route('switchRole', 'admin') }}" class="btn btn-danger">Admin</a>
    @endif

</body>

</html>