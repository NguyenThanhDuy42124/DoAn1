@extends('layouts.AdminDashBoard')
@section('content')
<div class="row justify-content-center"> 
                    <div class="col-12">
                        <div class="dashboard-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span>Quản lý Thông báo</span>
                                <div>
                                    <button class="btn btn-sm btn-secondary" data-toggle="collapse" data-target="#searchPanel">
                                        <i class="fas fa-search"></i> Tìm kiếm
                                    </button>
                                    <a class="btn btn-sm btn-primary" href="{{ route('admin.notifications.create') }}">
                                        <i class="fas fa-plus"></i> Thêm mới
                                    </a>
                                </div>
                            </div>

                            <!-- Collapse form tìm kiếm -->
                            <div class="collapse mt-2" id="searchPanel">
                                <form method="GET" action="{{ route('admin.notifications.index') }}" class="form-inline mb-3 p-3 bg-light rounded">
                                    <div class="form-group mr-2">
                                        <label for="filter-type" class="mr-2">Loại thông báo</label>
                                        <select id="filter-type" name="type" class="form-control form-control-sm mr-2">
                                            <option value="">Tất cả</option>
                                            <option value="system" {{ request('type') == 'system' ? 'selected' : '' }}>Hệ thống</option>
                                            <option value="user" {{ request('type') == 'user' ? 'selected' : '' }}>Người dùng</option>
                                            <option value="alert" {{ request('type') == 'alert' ? 'selected' : '' }}>Cảnh báo</option>
                                            <option value="info" {{ request('type') == 'info' ? 'selected' : '' }}>Thông tin</option>
                                        </select>
                                    </div>
                                    <div class="form-group mr-2">
                                        <label for="filter-status" class="mr-2">Trạng thái</label>
                                        <select id="filter-status" name="status" class="form-control form-control-sm mr-2">
                                            <option value="">Tất cả</option>
                                            <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Đã đọc</option>
                                            <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Chưa đọc</option>
                                        </select>
                                    </div>
                                    <div class="form-group mr-2">
                                        <input type="text" name="keyword" value="{{ request('keyword') }}"
                                            class="form-control form-control-sm" placeholder="Nhập từ khóa tìm kiếm"
                                            style="height: 38px;">
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="fas fa-filter"></i> Lọc
                                    </button>
                                </form>
                            </div>

                            <!-- bảng thông báo -->
                            <div class="card-body">
                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        {{ session('success') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif
                                
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th width="5%">ID</th>
                                                <th width="10%">User ID</th>
                                                <th width="15%">Loại</th>
                                                <th width="30%">Nội dung</th>
                                                <th width="10%">Trạng thái</th>
                                                <th width="15%">Ngày tạo</th>
                                                <th width="15%" class="text-center">Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($notifications as $notification)
                                            <tr class="{{ $notification->read ? 'notification-read' : 'notification-unread' }}">
                                                <td>{{ $notification->id }}</td>
                                                <td>{{ $notification->user_id }}</td>
                                                <td><span class="badge-type">{{ $notification->type }}</span></td>
                                                <td>{{ Str::limit($notification->message, 50) }}</td>
                                                <td>
                                                    @if($notification->read)
                                                        <span class="badge badge-success">Đã đọc</span>
                                                    @else
                                                        <span class="badge badge-warning">Chưa đọc</span>
                                                    @endif
                                                </td>
                                                <td>{{ $notification->created_at->format('d/m/Y H:i') }}</td>
                                          <td class="text-center">
  <!--  <a href="{{ route('admin.notifications.edit', $notification->id) }}" class="btn btn-sm btn-warning">
        <i class="fas fa-edit"></i> -->
    </a>
    <form action="{{ route('admin.notifications.destroy', $notification->id) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa?')">
            <i class="fas fa-trash"></i>
        </button>
    </form>
    @if(!$notification->is_read)
        <form action="{{ route('admin.notifications.markAsRead', $notification->id) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-sm btn-success">
                <i class="fas fa-check"></i>
            </button>
        </form>
    @else
        <form action="{{ route('admin.notifications.markAsUnread', $notification->id) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-sm btn-secondary">
                <i class="fas fa-undo"></i>
            </button>
        </form>
    @endif
</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
@endsection