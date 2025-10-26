@extends('layouts.account')

@section('title', 'Thông tin cá nhân')

{{-- Nạp nội dung vào vùng @yield('account_content') của layout cha --}}
@section('account_content')

<div class="card shadow-sm border-0">

    {{-- Header: Tiêu đề và nút Chỉnh sửa --}}
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Hồ sơ của tôi</h5>
        
        {{-- Nút Chỉnh sửa (lấy từ cuối file gốc) --}}
        @if(Auth::user()->role == 'admin')
            <a href="{{ route('users.edit', Auth::user()->id) }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-pencil-alt"></i> Chỉnh sửa
            </a>
        @elseif(Auth::user()->role == 'buyer' || Auth::user()->role == 'seller')
            <a href="{{ route('general.users.edit', Auth::user()->id) }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-pencil-alt"></i> Chỉnh sửa
            </a>
        @endif
    </div>

    {{-- Body: Toàn bộ thông tin --}}
    <div class="card-body">
        
        {{-- Xóa thẻ <h4 class="mb-4"> vì đã có tiêu đề --}}

        <div class="info-item">
            <div class="info-label">Họ và tên:</div>
            <div class="info-value">{{ Auth::user()->name }}</div>
        </div>

        <div class="info-item">
            <div class="info-label">Email:</div>
            <div class="info-value">{{ Auth::user()->email }}</div>
        </div>

        <div class="info-item">
            <div class="info-label">Số điện thoại:</div>
            <div class="info-value">{{ Auth::user()->phoneNumber }}</div>
        </div>

        <div class="info-item">
            <div class="info-label">Ngày sinh:</div>
            <div class="info-value">{{ \Carbon\Carbon::parse(Auth::user()->dateOfBirth)->format('d/m/Y') }}</div>
        </div>

        <div class="info-item">
            <div class="info-label">Giới tính:</div>
            <div class="info-value text-capitalize">{{ Auth::user()->gender }}</div>
        </div>

        @if(Auth::user()->role !== 'admin' && auth()->user()->address != null)
            <div class="info-item">
                <div class="info-label">Địa chỉ:</div>
                <div class="info-value">{{ Auth::user()->address }}</div>
            </div>
        @endif

        <div class="info-item">
            <div class="info-label">Quyền hạn:</div>
            <div class="info-value text-capitalize">{{ Auth::user()->role }}</div>
        </div>
    </div>

    {{-- Footer: Các nút chuyển vai trò (nếu có) --}}
    @if(Auth::user()->role == 'seller' || Auth::user()->role == 'admin')
        <div class="card-footer bg-white py-3">
            {{-- Xóa thẻ div.mt-4 pt-4 border-top cũ --}}
            
            @if(Auth::user()->role == 'seller')
            <a href="{{ route('switchRole', 'seller') }}" class="btn btn-primary mr-2">Chuyển sang Seller</a>
            @endif

            @if(Auth::user()->role == 'admin')
            <a href="{{ route('switchRole', 'admin') }}" class="btn btn-danger mr-2">Chuyển sang Admin</a>
            @endif
            
            {{-- Các nút chỉnh sửa đã được chuyển lên header --}}
        </div>
    @endif

</div>

@endsection