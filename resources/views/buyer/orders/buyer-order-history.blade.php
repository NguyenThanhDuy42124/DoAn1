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
                                        <th>Hành động (Đơn hàng)</th> <th>Đánh giá SP</th> </tr>
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
                                                
                                                <td>
                                                   @if ($order->status === 'Completed')
            
            @php
                // Kiểm tra xem collection $order->reviews (chỉ của đơn hàng này)
                // có review nào khớp với product_id của item hiện tại không.
                $reviewed = $order->reviews
                    ->where('product_id', $item->product_id)
                    ->isNotEmpty();
            @endphp
            
            @if ($reviewed)
                <span class="badge bg-success">Đã đánh giá</span>
            @else
                <button 
                    type="button" 
                    class="btn btn-info btn-sm"
                    {{-- Logic này đã đúng (có order_id) --}}
                    wire:click.prevent="openReviewModal({{ $item->product_id }}, {{ $order->id }})">
                    Đánh giá
                </button>
            @endif
            @endif
                                                </td>
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

    @if ($showReviewModal)
    <div class="modal fade show" tabindex="-1" style="display: block; background-color: rgba(0,0,0,0.5);" aria-labelledby="reviewModalLabel" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reviewModalLabel">Đánh giá & nhận xét</h5>
                    <button type="button" class="btn-close" wire:click="closeReviewModal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($alreadyReviewed)
                        <div class="alert alert-warning">Bạn đã đánh giá sản phẩm này rồi.</div>
                    
                    @elseif ($productToReview)
                        <div class="d-flex align-items-center mb-3">
                            <img src="{{ $productToReview->thumbnail_url ?? 'https://via.placeholder.com/100' }}" alt="{{ $productToReview->name }}" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: contain;">
                            <h6 class="ms-3">{{ $productToReview->name }}</h6>
                        </div>

                        <form wire:submit.prevent="submitReview">
                            {{-- 
                                GHI CHÚ: Hình ảnh modal (image_bb98a2.png) của bạn có 3 tiêu chí (Pin, Hiệu năng, Màn hình).
                                Tuy nhiên, CSDL (image_bb991b.png) của bạn chỉ có 1 cột 'rating'.
                                Do đó, tôi sẽ triển khai 1 "Đánh giá chung" duy nhất để khớp với CSDL.
                            --}}
                            <div class="mb-3 text-center">
                                <label class="form-label d-block">Đánh giá chung</label>
                                <div class="rating-stars">
                                    @foreach(range(1, 5) as $star)
                                        {{-- Bạn cần có Font Awesome để hiển thị icon 'fas fa-star' --}}
                                        <i class="fas fa-star" 
                                           wire:click="$set('rating', {{ $star }})" 
                                           style="font-size: 2rem; cursor: pointer; color: {{ $rating >= $star ? '#ffc107' : '#e0e0e0' }}; transition: color 0.2s;">
                                        </i>
                                    @endforeach
                                </div>
                                @error('rating') <span class="text-danger d-block mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- Nhận xét chi tiết --}}
                            <div class="mb-3">
                                <label for="commentText" class="form-label">Xin mời chia sẻ một số cảm nhận</label>
                                <textarea class="form-control @error('comment') is-invalid @enderror" id="commentText" rows="4" wire:model.defer="comment" placeholder="Nhận xét của bạn (tối thiểu 15 ký tự)"></textarea>
                                @error('comment') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            {{-- Nút Gửi --}}
                            <button type="submit" class="btn btn-danger w-100" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="submitReview">
                                    GỬI ĐÁNH GIÁ
                                </span>
                                <span wire:loading wire:target="submitReview">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    Đang gửi...
                                </span>
                            </button>
                        </form>
                    
                    @else
                        <div class="alert alert-danger">Đã có lỗi xảy ra. Vui lòng thử lại.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    {{-- Lớp phủ (backdrop) cho modal --}}
    <div class="modal-backdrop fade show"></div>
    @endif
</div>