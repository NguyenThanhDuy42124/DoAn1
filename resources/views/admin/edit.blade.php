<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/css/register.css', 'resources/js/app.js'])
    <title>Document</title>
</head>
<body>
    @if (session()->has('message'))
        <h3 style="align-self: center">{{ session('message') }}</h3>
    @endif
    <form action="{{ route('users.update', $user->id) }}" method="POST" class="form-sample">
        @csrf
        @method('PUT')
        <h5>Thông tin người dùng</h5>
        <div class="form-group">
            <label for="name">Họ và tên</label>
            <input type="text" id="name" name="name"  value="{{ $user->name }}" placeholder="Nhập họ và tên đầy đủ" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ $user->email }}" placeholder="Nhập địa chỉ email" required>
        </div>
        <div class="form-group">
            <label for="password">Mật Khẩu</label>
            <input type="password" id="password" name="password" value="{{ $user->password }}" placeholder="Tạo mật khẩu mạnh" required>
        </div>
        <div class="form-group">
            <label for="role">Vai trò</label>
            <select id="role" name="role" required>
                <option value="buyer" {{ $user->role === 'buyer' ? 'selected' : '' }}>Buyer</option>
                <option value="seller" {{ $user->role === 'seller' ? 'selected' : '' }}>Seller</option>
            </select>

        </div>
        <button type="submit" class="btn btn-register">Mới</button>

    </form>
       <!-- <form class="form-e" action="" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name" >họ và tên</label>
                            <input type="text" class="form-control" id="name" name="name" value="" placeholder="Nhập họ và tên đầy đủ" required>
                        </div>
                        <div class="form-group">
                            <label for="email" >Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="" placeholder="Nhập địa chỉ email" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Mật Khẩu</label>
                            <input type="password" class="form-control" id="password" value="" name="password" placeholder="Tạo mật khẩu mạnh" required>
                        </div>
                        <button type="submit" class="btn btn-register">Mới</button>
                    </form> -->
</body>
</html>
