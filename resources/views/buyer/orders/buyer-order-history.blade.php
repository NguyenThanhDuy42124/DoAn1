<div>
    {{-- Flash messages (Đã sửa cho chuẩn Bootstrap 5) --}}
    @if(session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Lịch sử mua hàng</h5>
        </div>

        <div class="card-body">
            {{-- Giữ nguyên logic Tabs của bạn --}}
            <ul class="nav nav-tabs" id="orderTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $status === 'Pending' ? 'active' : '' }}" href="#" 
                       wire:click.prevent="updateStatus('Pending')">
                       Chờ xác nhận ({{ $pendingCount }})
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $status === 'Shipping' ? 'active' : '' }}" href="#" 
                       wire:click.prevent="updateStatus('Shipping')">
                       Đang giao ({{ $shippingCount }})
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $status === 'Delivered' ? 'active' : '' }}" href="#" 
                       wire:click.prevent="updateStatus('Delivered')">
                       Đã giao ({{ $deliveredCount }})
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $status === 'Cancelled' ? 'active' : '' }}" href="#" 
                       wire:click.prevent="updateStatus('Cancelled')">
                       Đã hủy ({{ $cancelledCount }})
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $status === 'Returned' ? 'active' : '' }}" href="#" 
                       wire:click.prevent="updateStatus('Returned')">
                       Trả hàng ({{ $returnedCount }})
                    </a>
                </li>
            </ul>

            {{-- Giữ nguyên logic Tab Content của bạn --}}
            <div class="tab-content mt-3">
                <div class="tab-pane fade show active" role="tabpanel">

                    @if ($orders->isEmpty())
                        {{-- Giao diện khi không có đơn hàng --}}
                        <div class="text-center py-5">
                            <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Không có đơn hàng nào.</h5>
                            <p class="text-muted mb-0">Bạn chưa có đơn hàng nào trong mục này.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light">
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
                                    {{-- Giữ nguyên logic lồng ghép đơn hàng của bạn --}}
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
                                                        @elseif ($order->status === 'Delivered' || $order->status === 'Completed')
                                                            @if($order->status === 'Delivered')
                                                            <button type="button" wire:click.prevent="confirmOrder({{ $order->id }})" onclick="if(!confirm('Xác nhận đã nhận hàng?')) return false;" class="btn btn-success btn-sm">Đã nhận</button>
                                                            <button type="button" wire:click.prevent="returnOrder({{ $order->id }})" onclick="if(!confirm('Yêu cầu trả hàng?')) return false;" class="btn btn-warning btn-sm mt-1">Trả hàng</button>
                                                            @elseif($order->status === 'Completed')
                                                            <a href="#" class="btn btn-info btn-sm">Đánh giá</a>
                                                            <form action="{{ route('orders.repurchase', $order->id) }}" method="POST" class="d-inline mt-1">
                                                                @csrf
                                                                <button type="submit" class="btn btn-warning btn-sm">Mua lại</button>
                                                            </form>
                                                            @endif
                                                        @elseif ($order->status == 'Cancelled')
                                                            <form action="{{ route('orders.repurchase', $order->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-warning btn-sm">Mua lại</button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-3">
                            {{ $orders->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>