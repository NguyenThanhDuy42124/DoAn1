@extends('layouts.SellerDashBoard')
@section('content')
 <div class="container">
    <h2 class="mb-4 mt-4">Chỉnh Sửa Voucher</h2>

    <form action="{{ route('vouchers.update', $voucher->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Tỉ lệ giảm giá (%)</label>
            <input type="number" step="0.1" name="discount_rate" class="form-control" 
                   value="{{ old('discount_rate', $voucher->discount_rate) }}" required>
            @error('discount_rate')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Điều kiện áp dụng</label>
            <input type="text" name="condition" class="form-control"
                   value="{{ old('condition', $voucher->condition) }}">
            @error('condition')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Ngày hết hạn</label>
            <input type="date" name="expiry_date" class="form-control"
                   value="{{ old('expiry_date', $voucher->expiry_date->format('Y-m-d')) }}" required>
            @error('expiry_date')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-success">Cập Nhật</button>
        <a href="{{ route('vouchers.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection