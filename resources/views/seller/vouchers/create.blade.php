@extends('layouts.SellerDashBoard')
@section('content')
<div class="container">
    <h2 class="mb-4 mt-4">Tạo Voucher Mới</h2>

    <form action="{{ route('vouchers.store') }}" method="POST">
        @csrf

        <input type="hidden" name="seller_id" value="{{ auth()->id() }}">

        <div class="mb-3">
            <label class="form-label">Tỉ lệ giảm giá (%)</label>
            <input type="number" step="0.1" name="discount_rate" class="form-control" required>
            @error('discount_rate')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Điều kiện áp dụng</label>
            <input type="text" name="condition" class="form-control">
            @error('condition')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Ngày hết hạn</label>
            <input type="date" name="expiry_date" class="form-control" required>
            @error('expiry_date')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Tạo Voucher</button>
        <a href="{{ route('vouchers.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection