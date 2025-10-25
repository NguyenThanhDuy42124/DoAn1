{{-- resources/views/dashboard.blade.php (ĐÃ XÂY LẠI) --}}
@extends('layouts.account')

@section('title', 'Thông tin cá nhân')

{{-- Nạp nội dung vào vùng @yield('account_content') của layout cha --}}
@section('account_content')

    <h4 class="mb-4">Thông tin cá nhân</h4>

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

    <div class="mt-4 pt-4 border-top">
        @if(Auth::user()->role == 'seller')
        <a href="{{ route('switchRole', 'seller') }}" class="btn btn-primary mr-2">Chuyển sang Seller</a>
        @endif

        @if(Auth::user()->role == 'admin')
        <a href="{{ route('switchRole', 'admin') }}" class="btn btn-danger mr-2">Chuyển sang Admin</a>
        @endif

        @if(Auth::user()->role == 'admin')
        <a href="{{ route('users.edit', Auth::user()->id) }}" class="btn btn-outline-primary">Chỉnh sửa thông tin</a>
        @elseif(Auth::user()->role == 'buyer' || Auth::user()->role == 'seller')
        <a href="{{ route('general.users.edit', ['id' => Auth::user()->id, 'absolute' => true]) }}" class="btn btn-outline-primary">Chỉnh sửa thông tin</a>
        @endif
    </div>

@endsection