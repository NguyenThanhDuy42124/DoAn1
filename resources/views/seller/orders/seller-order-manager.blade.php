<div class="container mt-4">


    <!-- Flash messages -->


    <!-- Tabs -->
    <ul class="nav nav-tabs mt-4" id="orderTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link {{ $status === 'Pending' ? 'active' : '' }}" href="#"
                wire:click.prevent="filterByStatus('Pending')">
                Chờ xác nhận ({{ $pendingCount }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'Shipping' ? 'active' : '' }}" href="#"
                wire:click.prevent="filterByStatus('Shipping')">
                Đang giao hàng ({{ $shippingCount }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'Delivered' ? 'active' : '' }}" href="#"
                wire:click.prevent="filterByStatus('Delivered')">
                Đã giao ({{ $deliveredCount }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'Completed' ? 'active' : '' }}" href="#"
                wire:click.prevent="filterByStatus('Completed')">
                Hoàn thành ({{ $completedCount }})
            </a>
        </li>
    </ul>
    <div class="mt-3">
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center justify-content-between"
                role="alert">
                <div>
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                </div>
                {{-- Sử dụng btn-close thay vì class close cũ --}}
                <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center justify-content-between"
                role="alert">
                <div>
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                </div>
                <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>
    <div class="tab-content mt-3">
        <div class="tab-pane fade show active">
            <!-- Bulk form chỉ cho Pending -->
            @if ($status === 'Pending')
                <form wire:submit.prevent="bulkApprove">
                    @csrf
                    <button type="submit" class="btn btn-success mb-3">Duyệt hàng loạt</button>
                </form>
            @endif

            <!-- Table -->
            <table class="table table-bordered">
                <thead>
                    <tr>
                        @if ($status === 'Pending')
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
                            @if ($status === 'Pending')
                                <td><input type="checkbox" wire:model.live="selectedOrders"
                                        value="{{ $order->id }}"></td>
                            @endif
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->buyer->name ?? $order->buyer_name }}</td>
                            <td>{{ number_format($order->total_price) }} VND</td>
                            <td>
                                @php
                                    $statusColors = [
                                        'Pending' => 'bg-warning text-dark', // Vàng
                                        'Shipping' => 'bg-info text-dark', // Xanh dương nhạt
                                        'Delivered' => 'bg-primary', // Xanh dương đậm
                                        'Completed' => 'bg-success', // Xanh lá
                                        'Cancelled' => 'bg-danger', // Đỏ
                                        'Returned' => 'bg-secondary', // Xám
                                    ];

                                    $statusNames = [
                                        'Pending' => 'Chờ xác nhận',
                                        'Shipping' => 'Đang giao',
                                        'Delivered' => 'Đã giao',
                                        'Completed' => 'Hoàn thành',
                                        'Cancelled' => 'Đã hủy',
                                        'Returned' => 'Trả hàng',
                                    ];
                                @endphp

                                <span class="badge {{ $statusColors[$order->status] ?? 'bg-secondary' }}">
                                    {{ $statusNames[$order->status] ?? $order->status }}
                                </span>
                            </td>
                            <td>{{ $order->payment_status_vn }}</td>
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="#" class="btn btn-info btn-sm">Xem
                                    chi tiết</a>
                                @if ($order->status === 'Pending')
                                    <button type="button" wire:click="updateStatus({{ $order->id }}, 'Shipping')"
                                        class="btn btn-primary btn-sm ml-2">Xác nhận</button>
                                @elseif ($order->status === 'Shipping')
                                    <button type="button" wire:click="updateStatus({{ $order->id }}, 'Delivered')"
                                        class="btn btn-success btn-sm ml-2">Giao hàng</button>
                                @elseif ($order->status === 'Completed')
                                    <button type="button" wire:click.prevent="openReviewModal({{ $order->id }})"
                                        class="btn btn-warning btn-sm ml-2">
                                        Xem đánh giá
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            {{ $orders->links() }}

            @if ($status === 'Pending')
                </form> <!-- Đóng form bulk -->
            @endif
        </div>
    </div>
    @if ($showReviewModal && $orderForReview)
        <div class="modal fade show" tabindex="-1" style="display: block; background-color: rgba(0,0,0,0.5);"
            aria-labelledby="reviewModalLabel" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="reviewModalLabel">
                            Đánh giá cho Đơn hàng #{{ $orderForReview->id }}
                        </h5>
                        <button type="button" wire:click="closeReviewModal" aria-label="Close"><i
                                class="fas fa-times"></i></button>
                    </div>
                    <div class="modal-body">

                        {{-- Thông báo thành công/lỗi khi trả lời --}}
                        @if (session()->has('reply_success'))
                            <div class="alert alert-success">{{ session('reply_success') }}</div>
                        @endif
                        @if (session()->has('reply_error'))
                            <div class="alert alert-danger">{{ session('reply_error') }}</div>
                        @endif

                        @php
                            // Lấy tất cả review CỦA ĐƠN HÀNG NÀY (đã được tải trong openReviewModal)
                            $allReviews = $orderForReview->reviews;
                        @endphp

                        @if ($allReviews->isEmpty())
                            <div class="text-center p-4">
                                <i class="fas fa-comment-slash fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Chưa có đánh giá nào</h5>
                                <p class="text-muted">Đơn hàng này chưa nhận được đánh giá nào từ người mua.</p>
                            </div>
                        @else
                            {{-- 
                                Vòng lặp này bây giờ đã đúng
                                Vì $review->product và $review->buyer đã được tải 
                                trong hàm openReviewModal() của SellerOrderManager.php
                            --}}
                            @foreach ($allReviews as $review)
                                <div class="border rounded p-3 mb-3">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong>Sản phẩm:</strong> {{ $review->product->name }}
                                        </div>
                                        <span
                                            class="text-muted small">{{ $review->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <hr class="my-2">
                                    <div class="d-flex">
                                        {{-- Avatar người mua (Đã sửa theo yêu cầu trước) --}}
                                        <img src="{{ !empty($review->buyer->img)
                                            ? asset('storage/' . $review->buyer->img)
                                            : asset('storage/profile_images/default.jpg') }}"
                                            class="rounded-circle me-3"
                                            style="width: 50px; height: 50px; object-fit: cover;"
                                            alt="{{ $review->buyer->name }}">

                                        <div class="w-100">
                                            <strong>{{ $review->buyer->name ?? 'Người dùng' }}</strong>

                                            <div class="text-warning mb-1">
                                                @for ($i = 0; $i < 5; $i++)
                                                    <i
                                                        class="fas fa-star {{ $i < $review->rating ? '' : 'text-muted' }}"></i>
                                                @endfor
                                            </div>
                                            <p classs="mb-2">{{ $review->comment }}</p>

                                            <div class="bg-light p-3 rounded">
                                                <form wire:submit.prevent="submitReply({{ $review->id }})">
                                                    <label for="reply-{{ $review->id }}"
                                                        class="form-label fw-bold">Phản hồi của
                                                        bạn:</label>
                                                    <textarea class="form-control" id="reply-{{ $review->id }}" rows="3"
                                                        placeholder="Viết phản hồi cho khách hàng..." wire:model.defer="replies.{{ $review->id }}">
                                                    </textarea>
                                                    <button type="submit" class="btn btn-primary btn-sm mt-2"
                                                        wire:loading.attr="disabled"
                                                        wire:target="submitReply({{ $review->id }})">
                                                        <span wire:loading.remove
                                                            wire:target="submitReply({{ $review->id }})">
                                                            <i class="fas fa-save me-1"></i> Lưu Phản hồi
                                                        </span>
                                                        <span wire:loading
                                                            wire:target="submitReply({{ $review->id }})">
                                                            <span class="spinner-border spinner-border-sm"
                                                                role="status" aria-hidden="true"></span>
                                                            Đang lưu...
                                                        </span>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeReviewModal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>
        {{-- Lớp phủ (backdrop) cho modal --}}
        <div class="modal-backdrop fade show"></div>
    @endif
</div>
</div>
