@extends('layouts.AdminDashBoard')
@section('content')
 <div class="col-12">
    <div class="dashboard-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Tạo người dùng mới</span>
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

            <!-- Form tạo tài khoản -->
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   placeholder="Nhập họ và tên đầy đủ" 
                                   value="{{ old('name') }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   placeholder="Nhập địa chỉ email" 
                                   value="{{ old('email') }}" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Tạo mật khẩu mạnh" required>
                            <small class="form-text text-muted">
                                Mật khẩu phải có ít nhất 8 ký tự.
                            </small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password_confirmation" class="form-label">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password_confirmation" 
                                   name="password_confirmation" placeholder="Nhập lại mật khẩu" required>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.users.manager') }}" class="btn btn-secondary mr-2">Hủy bỏ</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Thêm mới
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection