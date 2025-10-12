@extends('layouts.app')

@section('content')
    <h1>Danh sách đơn hàng của bạn</h1>
    <form action="{{ route('seller.orders.bulk_approve') }}" method="POST">
        @csrf
        <table class="table">
            <thead>
                <tr>
                    <th><input type="checkbox" id="select-all"></th> <!-- Checkbox chọn tất cả -->
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
                            <a href="{{ route('seller.orders.show', $order->id) }}" class="btn btn-info">Xem chi tiết</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <button type="submit" class="btn btn-success">Duyệt hàng loạt</button>
    </form>
    {{ $orders->links() }}

    <script>
        document.getElementById('select-all').onclick = function() {
            var checkboxes = document.querySelectorAll('input[name="order_ids[]"]');
            for (var checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        }
    </script>
@endsection