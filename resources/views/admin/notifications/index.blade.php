@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Gửi Thông Báo</h2>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.notifications.store') }}" method="POST">
        @csrf
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
@endsection
