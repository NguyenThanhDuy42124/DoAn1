<div class="container mt-4">
    <h3>Lịch sử mua hàng</h3>
    
    <!-- Flash messages -->
    @if(session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if(session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Tabs -->
    <ul class="nav nav-tabs" id="orderTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link {{ $status === 'Pending' ? 'active' : '' }}" wire:navigate href="{{ route('buyer.orders.index', ['status' => 'Pending']) }}">Chờ xác nhận ({{ $pendingCount }})</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'Shipping' ? 'active' : '' }}" wire:navigate href="{{ route('buyer.orders.index', ['status' => 'Shipping']) }}">Đang giao ({{ $shippingCount }})</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'Delivered' ? 'active' : '' }}" wire:navigate href="{{ route('buyer.orders.index', ['status' => 'Delivered']) }}">Đã giao ({{ $deliveredCount }})</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'Cancelled' ? 'active' : '' }}" wire:navigate href="{{ route('buyer.orders.index', ['status' => 'Cancelled']) }}">Đã hủy ({{ $cancelledCount }})</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'Returned' ? 'active' : '' }}" wire:navigate href="{{ route('buyer.orders.index', ['status' => 'Returned']) }}">Trả hàng ({{ $returnedCount }})</a>
        </li>
    </ul>

    <div class="tab-content mt-3">
        <div class="tab-pane fade show active">
            <!-- Table -->
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
                        @foreach ($order->items as $index => $item)
                            <tr>
                                @if ($index === 0)
                                    <td rowspan="{{ count($order->items) }}">{{ $order->id }}</td>
                                    <td rowspan="{{ count($order->items) }}">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                @endif
                                <td>{{ $item->product->name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->price * $item->quantity, 0, ',', '.') }}₫</td>
                                @if ($index === 0)
                                    <td rowspan="{{ count($order->items) }}">{{ number_format($order->total_price, 0, ',', '.') }}₫</td>
                                    <td rowspan="{{ count($order->items) }}">{{ $order->status }}</td>
                                    <td rowspan="{{ count($order->items) }}">
                                        @if ($order->status === 'Pending')
                                            <button type="button" wire:click.prevent="cancelOrder({{ $order->id }})" onclick="if(!confirm('Bạn có chắc muốn hủy đơn hàng?')) return false;" class="btn btn-danger btn-sm">Hủy đơn</button>
                                        @elseif ($order->status === 'Delivered')
                                            <button type="button" wire:click.prevent="confirmOrder({{ $order->id }})" onclick="if(!confirm('Xác nhận đã nhận hàng?')) return false;" class="btn btn-success btn-sm">Đã nhận</button>
                                            <button type="button" wire:click.prevent="returnOrder({{ $order->id }})" onclick="if(!confirm('Yêu cầu trả hàng?')) return false;" class="btn btn-warning btn-sm mt-1">Trả hàng</button>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            {{ $orders->links() }}
        </div>
    </div>
</div>

<!-- Bootstrap JS cho alert -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"></script>