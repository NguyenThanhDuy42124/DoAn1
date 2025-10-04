<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
     @vite(['resources/css/usermanager.css', 'resources/js/app.js'])
    <title>Quản lý người dùng - TechStore</title>

</head>

<body style="padding-top: 0">
    <div class="wrapper pt-0">
        <!-- Content -->
        <div id="content">

            <!-- Main Content -->
            @if (session()->has('message'))
                <h3 style="align-self: center">{{ session('message') }}</h3>
                @endif
                <form action="{{ route('general.users.update', $user->id) }}" method="POST" class="form-sample">
    @csrf
    @method('PUT')
    <h5>Chỉnh sửa thông tin người dùng</h5>
    
    <div class="form-group">
        <label for="name">Họ và tên</label>
        <input type="text" id="name" name="name" value="{{ $user->name }}" placeholder="Nhập họ và tên đầy đủ" required>
        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
    </div>
    
    <div class="form-group">
        <label for="phoneNumber">Số điện thoại</label>
        <input type="text" id="phoneNumber" name="phoneNumber" value="{{ $user->phoneNumber }}" placeholder="Nhập số điện thoại" required>
        @error('phoneNumber') <span class="text-danger">{{ $message }}</span> @enderror
    </div>
    
    <div class="form-group">
        <label for="dateOfBirth">Ngày sinh</label>
        <input type="date" id="dateOfBirth" name="dateOfBirth" value="{{ $user->dateOfBirth }}" required>
        @error('dateOfBirth') <span class="text-danger">{{ $message }}</span> @enderror
    </div>
    
    <div class="form-group">
        <label for="gender">Giới tính</label>
        <select id="gender" name="gender" required>
            <option value="male" {{ $user->gender === 'male' ? 'selected' : '' }}>Nam</option>
            <option value="female" {{ $user->gender === 'female' ? 'selected' : '' }}>Nữ</option>
            <option value="other" {{ $user->gender === 'other' ? 'selected' : '' }}>Khác</option>
        </select>
        @error('gender') <span class="text-danger">{{ $message }}</span> @enderror
    </div>
   @if(Auth::user()->role == 'buyer')
    <div class="form-group">
        <label for="address">Địa chỉ giao hàng</label>
        <input type="address" id="address" name="address" value="{{ $user->address }}" placeholder="Nhập địa chỉ giao hàng" required>
        @error('email') <span class="text-danger">{{ $message }}</span> @enderror
    </div>
@elseif (Auth::user()->role == 'seller')
    <div class="form-group">
        <label for="address">Nhập địa chỉ shop</label>
        <input type="address" id="address" name="address" value="{{ $user->address }}" placeholder="Nhập địa chỉ shop" required>
        @error('email') <span class="text-danger">{{ $message }}</span> @enderror
    </div>
@endif

    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="{{ $user->email }}" placeholder="Nhập địa chỉ email" required>
        @error('email') <span class="text-danger">{{ $message }}</span> @enderror
    </div>
    
    <!-- Remove password field -->
    
    <button type="submit" class="btn btn-register">Lưu lại thông tin</button>
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
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>

    <script>
        $(document).ready(function () {
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
                $('#content').toggleClass('active');
            });
        });
    </script>
</body>

</html>
