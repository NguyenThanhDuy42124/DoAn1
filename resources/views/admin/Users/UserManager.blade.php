@extends('layouts.adminDashboard')
@section('title', 'Quản lý người dùng')
@section('content')
    <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="dashboard-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <!-- Search và Add Buttons -->
                                <span>Quản lý người dùng</span>
                                <div>
                                    <button class="btn btn-sm btn-secondary" data-toggle="collapse"
                                        data-target="#searchPanel">
                                        <i class="fas fa-search"></i> Tìm kiếm
                                    </button>
                                    <a class="btn btn-sm btn-primary" href="{{ route('users.create') }}">
                                        <i class="fas fa-plus"></i> Thêm mới
                                    </a>
                                </div>
                            </div>

                            <!-- Collapse search form -->
                            <div class="collapse mt-2" id="searchPanel" style="">
                                <form method="GET" action="{{ route('admin.dashboard') }}" class="form-inline mb-3"
                                    style="align-items: left;">
                                    <div class="form-group mr-2">
                                        <input type="text" name="keyword" value="{{ request('keyword') }}"
                                            class="form-control form-control-sm" placeholder="Nhập tên người dùng"
                                            style="height: 55px;">
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="fas fa-filter"></i> Lọc
                                    </button>
                                </form>
                            </div>

                            <!-- User Table -->
                            @if (session()->has('message'))
                            <h3 style="align-self: center">{{ session('message') }}</h3>
                            @endif
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Tên người dùng</th>
                                                <th>Email</th>
                                                <th>Vai trò</th>
                                                <th>Trạng thái</th>
                                                <th>Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($users as $user)
                                            <tr>
                                                <td>{{ $user->id }}</td>
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>
                                                    {{ $user->role }}
                                                    <!-- cái này nữa làm cái form select để đổi role
                                                        form này sẽ xuất hiện khi bấm nút edit ( edit sẽ thay đổi dc tên email vs role)
                                                        <select class="form-control" style="height: 50px;">
                                                        <option value="Buyer" {{ $user->role == 'buyer' ? 'selected' : '' }}>Buyer</option>
                                                        <option value="Seller" {{ $user->role == 'seller' ? 'selected' : '' }}>Seller</option>
                                                        <option value="Admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                                    </select>-->
                                                </td>
                                                <td><span class="badge badge-success">Active</span></td>
                                                <td>
                                                    <a class="btn btn-sm btn-info"
                                                        href="{{ route('users.edit', $user->id) }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                                        style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-sm btn-danger"><i
                                                                class="fas fa-trash"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                    <!-- Pagination -->
                                    {{ $users->links() }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="dashboard-card">
                            <div class="card-header">Gửi thông báo</div>
                            <div class="card-body">
                                <form>
                                    <div class="form-group">
                                        <label>Tiêu đề thông báo</label>
                                        <input type="text" class="form-control" placeholder="Nhập tiêu đề">
                                    </div>
                                    <div class="form-group">
                                        <label>Nội dung thông báo</label>
                                        <textarea class="form-control" rows="3"
                                            placeholder="Nhập nội dung thông báo"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Gửi đến</label>
                                        <select class="form-control" style="height: 50px;">
                                            <option>Tất cả người dùng</option>
                                            <option>Người dùng thường</option>
                                            <option>Người bán hàng</option>
                                            <option>Quản trị viên</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-block">Gửi thông báo</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
@endsection
