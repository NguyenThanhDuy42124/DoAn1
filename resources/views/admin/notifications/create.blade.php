@extends('layouts.AdminDashBoard')
@section('content')
   <div class="row justify-content-center"> 
                    <div class="col-12">
                        <div class="dashboard-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span>Tạo thông báo mới</span>
                                <div>
                                    <a class="btn btn-sm btn-secondary" href="{{ route('admin.notifications.index') }}">
                                        <i class="fas fa-arrow-left"></i> Quay lại
                                    </a>
                                </div>
                            </div>

                            <div class="card-body">
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <form action="{{ route('admin.notifications.store') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="user_id" class="form-label">User ID <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="user_id" name="user_id" placeholder="Nhập ID người dùng" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="type" class="form-label">Loại thông báo <span class="text-danger">*</span></label>
                                                <select class="form-control" id="type" name="type" required>
                                                    <option value="">-- Chọn loại thông báo --</option>
                                                    <option value="system">Hệ thống</option>
                                                    <option value="user">Người dùng</option>
                                                    <option value="alert">Cảnh báo</option>
                                                    <option value="info">Thông tin</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="message" class="form-label">Nội dung thông báo <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="message" name="message" rows="4" placeholder="Nhập nội dung thông báo..." required></textarea>
                                    </div>
                                 <!--   <div class="form-group">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="read" name="read" value="1">
                                            <label class="form-check-label" for="read">
                                                Đánh dấu là đã đọc
                                            </label>
                                        </div>
                                    </div> -->
                                    <div class="d-flex justify-content-end">
                                        <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary mr-2">Hủy bỏ</a>
                                        <button type="submit" class="btn btn-primary">Tạo thông báo</button>
                                    </div>
                                </form> 
                            </div>
                        </div>
                    </div>
                </div>
@endsection