<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    @vite(['resources/css/usermanager.css', 'resources/js/app.js'])
    <title>Quản lý đơn hàng - TechStore</title>
</head>
<body>
    @extends('layouts.app')

    @section('content')
    <div class="container mt-4">
        <h3>Quản lý đơn hàng</h3>
        <ul class="nav nav-tabs" id="orderTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link {{ $status === 'Pending' ? 'active' : '' }}" href="{{ route('seller.orders.index', ['status' => 'Pending']) }}">Pending ({{ $orders->where('status', 'Pending')->count() }})</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $status === 'Shipped' ? 'active' : '' }}" href="{{ route('seller.orders.index', ['status' => 'Shipped']) }}">Shipped ({{ $orders->where('status', 'Shipped')->count() }})</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $status === 'Delivered' ? 'active' : '' }}" href="{{ route('seller.orders.index', ['status' => 'Delivered']) }}">Delivered ({{ $orders->where('status', 'Delivered')->count() }})</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $status === 'Completed' ? 'active' : '' }}" href="{{ route('seller.orders.index', ['status' => 'Completed']) }}">Completed ({{ $orders->where('status', 'Completed')->count() }})</a>
            </li>
        </ul>

        <div class="tab-content mt-3">
            <div class="tab-pane fade show active">
                <form action="{{ route('seller.orders.bulk_approve') }}" method="POST">
                    @csrf
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="select-all"></th>
                                <th>ID</th>
                                <th>Buyer</th>
                                <th>Tổng giá</th>
                                <th>Trạng thái</th>
                                <th>Thanh toán</th>
                                <th>Ngày tạo</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td><input type="checkbox" name="order_ids[]" value="{{ $order->id }}"></td>
                                    <td>{{ $order->id }}</td>
                                    <td>{{ $order->buyer->name ?? $order->buyer_name }}</td>
                                    <td>{{ number_format($order->total_price) }} VND</td>
                                    <td>{{ $order->status }}</td>
                                    <td>{{ $order->payment_status }}</td>
                                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('seller.orders.show', $order->id) }}" class="btn btn-info btn-sm">Xem chi tiết</a>
                                        @if ($order->status === 'Pending')
                                            <form action="{{ route('seller.orders.update_status', $order->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <input type="hidden" name="status" value="Shipped">
                                                <button type="submit" class="btn btn-primary btn-sm ml-2">Xác nhận</button>
                                            </form>
                                        @elseif ($order->status === 'Shipped')
                                            <form action="{{ route('seller.orders.update_status', $order->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <input type="hidden" name="status" value="Delivered">
                                                <button type="submit" class="btn btn-success btn-sm ml-2">Giao hàng</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="submit" class="btn btn-success mt-2">Duyệt hàng loạt</button>
                </form>
                {{ $orders->links() }}
            </div>
        </div>
    </div>
    @endsection

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"></script>
    <script>
        document.getElementById('select-all').onclick = function() {
            var checkboxes = document.querySelectorAll('input[name="order_ids[]"]');
            for (var checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        }
    </script>
</body>
</html>