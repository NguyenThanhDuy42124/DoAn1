<div class="container mt-4">
    <h3>Quản lý đơn hàng</h3>
    
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
            <a class="nav-link {{ $status === 'Pending' ? 'active' : '' }}" wire:navigate href="{{ route('seller.orders.index', ['status' => 'Pending']) }}">Pending ({{ $pendingCount }})</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'Shipped' ? 'active' : '' }}" wire:navigate href="{{ route('seller.orders.index', ['status' => 'Shipped']) }}">Shipped ({{ $shippedCount }})</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'Delivered' ? 'active' : '' }}" wire:navigate href="{{ route('seller.orders.index', ['status' => 'Delivered']) }}">Delivered ({{ $deliveredCount }})</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'Completed' ? 'active' : '' }}" wire:navigate href="{{ route('seller.orders.index', ['status' => 'Completed']) }}">Completed ({{ $completedCount }})</a>
        </li>
    </ul>

    <div class="tab-content mt-3">
        <div class="tab-pane fade show active">
            <!-- Bulk form chỉ cho Pending -->
            @if($status === 'Pending')
                <form wire:submit.prevent="bulkApprove">
                    @csrf
                    <button type="submit" class="btn btn-success mb-3">Duyệt hàng loạt</button>
                </form>
            @endif

            <!-- Table -->
            <table class="table table-bordered">
                <thead>
                    <tr>
                        @if($status === 'Pending')
                            <th><input type="checkbox" wire:model.live="selectAll"></th>
                        @endif
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
                            @if($status === 'Pending')
                                <td><input type="checkbox" wire:model.live="selectedOrders" value="{{ $order->id }}"></td>
                            @endif
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->buyer->name ?? $order->buyer_name }}</td>
                            <td>{{ number_format($order->total_price) }} VND</td>
                            <td>{{ $order->status }}</td>
                            <td>{{ $order->payment_status }}</td>
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('seller.orders.show', $order->id) }}" class="btn btn-info btn-sm">Xem chi tiết</a>
                                @if ($order->status === 'Pending')
                                    <button type="button" wire:click="updateStatus({{ $order->id }}, 'Shipped')" class="btn btn-primary btn-sm ml-2">Xác nhận</button>
                                @elseif ($order->status === 'Shipped')
                                    <button type="button" wire:click="updateStatus({{ $order->id }}, 'Delivered')" class="btn btn-success btn-sm ml-2">Giao hàng</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            {{ $orders->links() }}

            @if($status === 'Pending')
                </form> <!-- Đóng form bulk -->
            @endif
        </div>
    </div>
</div>

<!-- Bootstrap JS (giữ để alert dismiss work, nhưng Livewire handle main) -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"></script>