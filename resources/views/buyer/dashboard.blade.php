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
            <div class="info-value">{{ Auth::user()->name ?? 'Chưa cập nhật' }}</div>
        </div>

        <div class="info-item">
            <div class="info-label">Email:</div>
            <div class="info-value">{{ Auth::user()->email ?? 'Chưa cập nhật' }}</div>
        </div>

        <div class="info-item">
            <div class="info-label">Số điện thoại:</div>
            <div class="info-value">{{ Auth::user()->phoneNumber ?? 'Chưa cập nhật' }}</div>
        </div>

        <div class="info-item">
            <div class="info-label">Ngày sinh:</div>
            <div class="info-value">
                {{ Auth::user()->date_of_birth ? \Carbon\Carbon::parse(Auth::user()->date_of_birth)->format('d/m/Y') : 'Chưa cập nhật' }}
            </div>
        </div>

        <div class="info-item">
            <div class="info-label">Giới tính:</div>
            <div class="info-value text-capitalize">{{ Auth::user()->gender ?? 'Chưa cập nhật' }}</div>
        </div>

        @if(Auth::user()->role !== 'admin' && auth()->user()->address != null)
        <div class="info-item">
            <div class="info-label">Địa chỉ:</div>
            <div class="info-value">{{ Auth::user()->address ?? 'Chưa cập nhật' }}</div>
        </div>
        @endif

        <div class="info-item">
            <div class="info-label">Quyền hạn:</div>
            <div class="info-value text-capitalize">{{ Auth::user()->role }}</div>
        </div>

        <div class="info-item">
            <div class="info-label">Trạng thái EKYC:</div>
            <div class="info-value ">
                @if(Auth::user()->ekyc_status == 'pending')
                    <span class="badge badge-info text-black">⌛ Đang chờ</span>
                @elseif(Auth::user()->ekyc_status == 'verified')
                    <span class="badge badge-success text-black">✅ Đã xác minh</span>
                @elseif(Auth::user()->ekyc_status == 'rejected')
                    <span class="badge badge-danger text-black">❌ Bị từ chối</span>
                @else
                    <span class="badge badge-secondary text-black">❌ Chưa nộp</span>
                @endif
            </div>
        </div>
        @if(Auth::user()->role == 'buyer')
        <div class="info-item">
            <div class="info-label">Trở thành Seller:</div>
            <div class="info-value ">
                <form action="{{ route('buyer.requestToBecomeSeller') }}" method="POST" style="display:inline;">
                    @csrf
                    <button class="btn btn-primary btn-sm" type="submit">
                        <i class="fas fa-user-plus"></i> Tham gia ngay
                    </button>
                </form>
            </div>
            <p class="text-danger small"> để trở thành seller cần phải EKYC thành công!</p>
        </div>
        @if(session('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        @endif
    </div>
</div>

@endsection
