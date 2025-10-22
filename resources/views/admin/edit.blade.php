@extends('layouts.AdminDashBoard')
@section('content')

   <div class="col-12">
    <div class="dashboard-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Chỉnh sửa thông tin người dùng</span>
            <div>
                <a class="btn btn-sm btn-secondary" href="{{ route('admin.users.manager') }}">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>

        <div class="card-body">
            <!-- Hiển thị thông báo -->
            @if (session()->has('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form chỉnh sửa thông tin người dùng -->
            <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label for="name" class="form-label">Họ và tên</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" placeholder="Nhập họ và tên đầy đủ" required>
                </div>

                <div class="form-group">
                    <label for="phoneNumber" class="form-label">Số điện thoại</label>
                    <input type="text" class="form-control" id="phoneNumber" name="phoneNumber" value="{{ $user->phoneNumber }}" placeholder="Nhập số điện thoại" required>
                </div>

                <div class="form-group">
                    <label for="dateOfBirth" class="form-label">Ngày sinh</label>
                    <input type="date" class="form-control" id="dateOfBirth" name="dateOfBirth" value="{{ $user->dateOfBirth }}" required>
                </div>

                <div class="form-group">
                    <label for="gender" class="form-label">Giới tính</label>
                    <select class="form-control" id="gender" name="gender" required>
                        <option value="male" {{ $user->gender === 'male' ? 'selected' : '' }}>Nam</option>
                        <option value="female" {{ $user->gender === 'female' ? 'selected' : '' }}>Nữ</option>
                        <option value="other" {{ $user->gender === 'other' ? 'selected' : '' }}>Khác</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" placeholder="Nhập địa chỉ email" required>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Mật Khẩu</label>
                    <input type="password" class="form-control" id="password" name="password" value="{{ $user->password }}" placeholder="Tạo mật khẩu mạnh" required>
                </div>

                <div class="form-group">
                    <label for="role" class="form-label">Vai trò</label>
                    <select class="form-control" id="role" name="role" required>
                        <option value="buyer" {{ $user->role === 'buyer' ? 'selected' : '' }}>Buyer</option>
                        <option value="seller" {{ $user->role === 'seller' ? 'selected' : '' }}>Seller</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $user->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.users.manager') }}" class="btn btn-secondary mr-2">Hủy bỏ</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Lưu thông tin
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
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
@endsection