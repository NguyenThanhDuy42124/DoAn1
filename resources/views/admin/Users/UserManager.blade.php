@extends('layouts.AdminDashBoard')
@section('content')
                        <div class="container">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span>Quản lý người dùng</span>
                                <div>
                                    <button class="btn btn-sm btn-secondary" data-toggle="collapse" data-target="#searchPanel">
                                        <i class="fas fa-search"></i> Tìm kiếm
                                    </button>
                                    <a class="btn btn-sm btn-primary" href="{{ route('users.create') }}">
                                        <i class="fas fa-plus"></i> Thêm mới
                                    </a>
                                </div>
                            </div>

                            <!-- Collapse search form -->
                            <div class="collapse mt-2" id="searchPanel">
                                <form method="GET" action="{{ route('admin.dashboard') }}" class="form-inline mb-3 p-3 bg-light rounded">
                                    <div class="form-group mr-2 flex-grow-1">
                                        <input type="text" name="keyword" value="{{ request('keyword') }}"
                                            class="form-control form-control-sm" placeholder="Nhập tên người dùng"
                                            style="height: 38px; width: 100%;">
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="fas fa-filter"></i> Lọc
                                    </button>
                                </form>
                            </div>

                            <!-- User Table -->
                            <div class="card-body">
                                @if (session()->has('message'))
                                <div class="alert alert-info alert-dismissible fade show" role="alert">
                                    {{ session('message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                @endif

                                <div class="table-responsive">
                                    <table class="table table-hover user-table">
                                        <thead>
                                            <tr>
                                                <th width="5%">ID</th>
                                                <th width="20%">Tên người dùng</th>
                                                <th width="25%">Email</th>
                                                <th width="15%">Vai trò</th>
                                                <th width="10%">Trạng thái</th>
                                                <th width="25%" class="text-center">Hành động</th>
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
                                                @if($user->status == 'inactive')
                                                    <td><span class="badge badge-warning">Inactive</span></td>
                                                @else
                                                    <td><span class="badge badge-success">Active</span></td>
                                                @endif
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
                                </div>
                            </div>
                        </div>
                   
@endsection