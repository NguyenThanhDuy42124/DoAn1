<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    @vite(['resources/css/usermanager.css', 'resources/js/app.js'])
    <title>Lịch sử mua hàng - TechStore</title>
</head>
<body>
    @extends('layouts.app')
    
    @section('content')
    <div class="container mt-4">
        <h3>Lịch sử mua hàng</h3>
        
        @if ($ordersGrouped->isEmpty())
            <p>Bạn chưa có đơn hàng nào.</p>
        @else
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Mã đơn hàng</th>
                        <th>Ngày đặt</th>
                        <th>Sản phẩm</th>
                        <th>Số lượng</th>
                        <th>Giá</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
@foreach ($ordersGrouped as $group)
    @php
        $firstOrder = $group->first(); // lấy thông tin chung
        // Kiểm tra group có status 'paid' không
        if($firstOrder->status !== 'paid') {
            continue; // bỏ qua group không phải paid
        }
    @endphp
    <tr>
        <td rowspan="{{ count($group->flatMap->items) }}">{{ $firstOrder->id }}</td>
        <td rowspan="{{ count($group->flatMap->items) }}">{{ $firstOrder->created_at->format('d/m/Y H:i') }}</td>
        @foreach ($group->flatMap->items as $index => $item)
            @if($index > 0) <tr> @endif
            <td>{{ $item->product->name }}</td>
            <td>{{ $item->quantity }}</td>
            <!-- Giá = price * quantity -->
            <td>{{ number_format($item->price * $item->quantity, 0, ',', '.') }}₫</td>
            @if($index === 0)
                @php
                    // Tổng tiền của group = sum(price * quantity) tất cả items
                    $totalPrice = $group->flatMap->items->sum(function($item){
                        return $item->price * $item->quantity;
                    });
                @endphp
                <td rowspan="{{ count($group->flatMap->items) }}">{{ number_format($totalPrice, 0, ',', '.') }}₫</td>
                <td rowspan="{{ count($group->flatMap->items) }}">{{ $firstOrder->status }}</td>
            @endif
            @if($index > 0) </tr> @endif
        @endforeach
    </tr>
@endforeach
</tbody>

            </table>
        @endif
        
        <a href="{{ route('dashboard') }}" class="btn btn-primary mt-3">Quay lại Dashboard</a>
    </div>
@endsection
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"></script>
</body>
</html>