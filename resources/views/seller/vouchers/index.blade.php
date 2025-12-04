@extends('layouts.SellerDashBoard')
@section('content')
<div class="container">
  
    {{-- 1. Phần Header (Được đưa lên đầu) --}}
    <div class="page-header mt-4">
        <div class="d-flex justify-content-between align-items-center">
            <div class="mb-3 d-flex">
                <a href="{{ route('vouchers.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-2"></i>Thêm Voucher
                </a>
            </div>
            <div class="mb-3">
                <a href="{{ route('seller.dashboard') }}" class="btn btn-primary">
                    <i class="fas fa-home mr-2"></i>Quay lại
                </a>
            </div>
        </div>
    </div>
    <div class="mt-3">
    {{-- 2. Phần Thông báo (Đã chuyển xuống dưới Header) --}}
    @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center justify-content-between" role="alert">
                <div>
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                </div>
                {{-- Sử dụng btn-close thay vì class close cũ --}}
                <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>
    {{-- 3. Bảng dữ liệu --}}
    @if($vouchers->count())
        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>STT</th>
                    <th>Giảm giá</th>
                    <th>Điều kiện</th>
                    <th>Ngày hết hạn</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vouchers as $voucher)
                    <tr>
                        <td>{{ $voucher->id }}</td>
                        <td>{{ $voucher->discount_rate }}%</td>
                        <td>{{ $voucher->condition ?? 'Không có' }}</td>
                        <td>{{ $voucher->expiry_date->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('vouchers.edit', $voucher->id) }}" class="btn btn-sm btn-primary">
                                Sửa
                            </a>
                            <form action="{{ route('vouchers.destroy', $voucher->id) }}" 
                                  method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    Xóa
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-3">
            {{ $vouchers->links() }}
        </div>
    @else
        <div class="alert alert-info text-center mt-3">
            Hiện chưa có voucher nào.
        </div>
    @endif
</div>
@endsection