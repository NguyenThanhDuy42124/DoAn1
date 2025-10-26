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
                    <label for="role" class="form-label">Vai trò</label>
                    <select class="form-control" style="height: 50px;" id="role" name="role" required>
                        <option value="buyer" {{ $user->role === 'buyer' ? 'selected' : '' }}>Buyer</option>
                        <option value="seller" {{ $user->role === 'seller' ? 'selected' : '' }}>Seller</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select class="form-control" style="height: 50px;   " id="status" name="status" required>
                        <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $user->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Ảnh người dùng hiện tại : </label>
                    <div class="image-preview-container mb-3">
                        @if($user->img)
                        <img src="{{ asset('storage/' . $user->img) }}" class="img-fluid" alt="User Image" style="max-width: 200px;">
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="delete_image" value="1" id="deleteImage">
                            <label class="form-check-label" for="deleteImage">
                                Xóa ảnh này
                            </label>
                        </div>
                        @else
                        <p>Chưa có ảnh đại diện.</p>
                        @endif
                    </div>

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
