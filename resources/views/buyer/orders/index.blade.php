@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3>Lịch sử mua hàng</h3>
    <ul class="nav nav-tabs" id="orderTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link {{ $status === 'Pending' ? 'active' : '' }}" href="{{ route('buyer.orders.index', ['status' => 'Pending']) }}">Chờ xác nhận ({{ $orders->where('status', 'Pending')->count() }})</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'Shipped' ? 'active' : '' }}" href="{{ route('buyer.orders.index', ['status' => 'Shipped']) }}">Đang giao ({{ $orders->where('status', 'Shipped')->count() }})</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'Delivered' ? 'active' : '' }}" href="{{ route('buyer.orders.index', ['status' => 'Delivered']) }}">Đã giao ({{ $orders->where('status', 'Delivered')->count() }})</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'Cancelled' ? 'active' : '' }}" href="{{ route('buyer.orders.index', ['status' => 'Cancelled']) }}">Đã hủy ({{ $orders->where('status', 'Cancelled')->count() }})</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'Returned' ? 'active' : '' }}" href="{{ route('buyer.orders.index', ['status' => 'Returned']) }}">Trả hàng ({{ $orders->where('status', 'Returned')->count() }})</a>
        </li>
    </ul>

    <div class="tab-content mt-3">
        <div class="tab-pane fade show active">
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
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            @foreach ($order->items as $index => $item)
                                @if ($index > 0) <tr> @endif
                                <td>{{ $item->product->name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->price * $item->quantity, 0, ',', '.') }}₫</td>
                                @if ($index === 0)
                                    <td rowspan="{{ count($order->items) }}">{{ number_format($order->total_price, 0, ',', '.') }}₫</td>
                                    <td rowspan="{{ count($order->items) }}">{{ $order->status }}</td>
                                    <td rowspan="{{ count($order->items) }}">
                                        @if ($order->status === 'Pending')
                                            <form action="{{ route('buyer.orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-danger btn-sm">Hủy đơn</button>
                                            </form>
                                        @elseif ($order->status === 'Delivered')
                                            <form action="{{ route('buyer.orders.confirm', $order->id) }}" method="POST" onsubmit="return confirm('Xác nhận đã nhận hàng?');">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">Đã nhận</button>
                                            </form>
                                            <form action="{{ route('buyer.orders.return', $order->id) }}" method="POST" onsubmit="return confirm('Yêu cầu trả hàng?');">
                                                @csrf
                                                <button type="submit" class="btn btn-warning btn-sm mt-1">Trả hàng</button>
                                            </form>
                                        @endif
                                    </td>
                                @endif
                                @if ($index > 0) </tr> @endif
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection