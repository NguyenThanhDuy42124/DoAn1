<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    @vite(['resources/css/vouchers.css', 'resources/js/app.js'])
    <title>Danh sách khuyến mãi</title>
</head>
<body>
    <div class="container">
    <h2 class="mb-4">Tạo Voucher Mới</h2>

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
</body>
