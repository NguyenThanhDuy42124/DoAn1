<div>
    {{-- Flash messages (Giữ nguyên) --}}
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
            {{-- Tabs (Giữ nguyên) --}}
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

            {{-- Tab Content (Giữ nguyên) --}}
            <div class="tab-content mt-3">
                <div class="tab-pane fade show active" role="tabpanel">

                    @if ($orders->isEmpty())
                        {{-- Giao diện khi không có đơn hàng (Giữ nguyên) --}}
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
                                        <th>Hành động (Đơn hàng)</th>
                                        <th>Đánh giá SP</th> {{-- Giữ nguyên --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Lặp đơn hàng (Giữ nguyên) --}}
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
                                                        {{-- Actions (Giữ nguyên) --}}
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
                                                   {{-- ============================================== --}}
                                                   {{-- CẬP NHẬT LOGIC NÚT ĐÁNH GIÁ --}}
                                                   {{-- ============================================== --}}
                                                   @if ($order->status === 'Completed')
            
                                                        @php
                                                            // Kiểm tra xem đã review cho đơn hàng VÀ sản phẩm này chưa
                                                            $reviewed = $order->reviews
                                                                ->where('product_id', $item->product_id)
                                                                ->where('order_id', $order->id) // Đảm bảo đúng đơn hàng
                                                                ->isNotEmpty();
                                                        @endphp
                                                        
                                                        @if ($reviewed)
                                                            {{-- ĐÃ ĐÁNH GIÁ: Đổi thành nút "Xem Đánh giá" --}}
                                                            <button 
                                                                type="button" 
                                                                class="btn btn-secondary btn-sm"
                                                                wire:click.prevent="openReviewModal({{ $item->product_id }}, {{ $order->id }})">
                                                                Xem Đánh giá
                                                            </button>
                                                        @else
                                                            {{-- CHƯA ĐÁNH GIÁ: Nút "Đánh giá" như cũ --}}
                                                            <button 
                                                                type="button" 
                                                                class="btn btn-info btn-sm"
                                                                wire:click.prevent="openReviewModal({{ $item->product_id }}, {{ $order->id }})">
                                                                Đánh giá
                                                            </button>
                                                        @endif
                                                    @endif
                                                   {{-- ============================================== --}}
                                                   {{-- KẾT THÚC CẬP NHẬT --}}
                                                   {{-- ============================================== --}}
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

    {{-- =================================================================== --}}
    {{-- MODAL ĐÁNH GIÁ (ĐÃ CẬP NHẬT) --}}
    {{-- =================================================================== --}}
    @if ($showReviewModal)
    <div class="modal fade show" tabindex="-1" style="display: block; background-color: rgba(0,0,0,0.5);" aria-labelledby="reviewModalLabel" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reviewModalLabel">Đánh giá & nhận xét</h5>
                    <button type="button" class="btn-close" wire:click="closeReviewModal" aria-label="Close"></button>
                </div>

                {{-- NỘI DUNG MODAL --}}
                <div class="modal-body">

                    @if (session()->has('error_modal'))
                        <div class="alert alert-danger">{{ session('error_modal') }}</div>
                    @endif

                    @if ($productToReview)
                        {{-- 1. Thông tin sản phẩm (Giữ nguyên) --}}
                        <div class="d-flex align-items-center mb-3">
                            <img src="{{ $productToReview->thumbnail_url ?? 'https://via.placeholder.com/100' }}" alt="{{ $productToReview->name }}" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: contain;">
                            <h6 class="ms-3">{{ $productToReview->name }}</h6>
                        </div>
                        <hr>

                        @if ($alreadyReviewed && $existingReview)
                            {{-- =========================================== --}}
                            {{-- CASE 2: ĐÃ ĐÁNH GIÁ (CHẾ ĐỘ XEM/PHẢN HỒI) --}}
                            {{-- =========================================== --}}
                            
                            <h5 class="text-center">Lịch sử Đánh giá</h5>

                            {{-- 2a. Đánh giá gốc của Buyer (Chỉ đọc) --}}
                            <div class="mb-3 text-center">
                                <label class="form-label d-block">Đánh giá (Gốc)</label>
                                <div class="rating-stars">
                                    @foreach(range(1, 5) as $star)
                                        <i class="fas fa-star" 
                                           style="font-size: 2rem; color: {{ $existingReview->rating >= $star ? '#ffc107' : '#e0e0e0' }};">
                                        </i>
                                    @endforeach
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nhận xét (Gốc):</label>
                                <textarea class="form-control" rows="3" readonly disabled>{{ $comment }}</textarea>
                            </div>

                            {{-- 2b. Phản hồi của Seller (Nếu có) --}}
                            @if (!empty($existingReview->reply))
                                <div class="alert alert-secondary mt-3">
                                    <h6 class="alert-heading fw-bold">Phản hồi từ Người bán:</h6>
                                    <p class="mb-0" style="white-space: pre-wrap;">{{ $existingReview->reply }}</p>
                                </div>

                                {{-- 2c. Phản hồi bổ sung của Buyer (Nếu có) --}}
                                @if (!empty($existingReview->buyer_additional_feedback))
                                    {{-- Đã gửi phản hồi bổ sung -> Chỉ hiển thị --}}
                                    <div class="alert alert-info mt-3">
                                        <h6 class="alert-heading fw-bold">Phản hồi bổ sung của bạn (Đã cập nhật {{$existingReview->rating}} sao):</h6>
                                        <p class="mb-0" style="white-space: pre-wrap;">{{ $existingReview->buyer_additional_feedback }}</p>
                                    </div>
                                @else
                                    {{-- ============================================== --}}
                                    {{-- FORM GỬI PHẢN HỒI BỔ SUNG (ĐÃ CẬP NHẬT) --}}
                                    {{-- ============================================== --}}
                                    <form wire:submit.prevent="submitAdditionalFeedback" class="mt-3 border p-3 rounded bg-light">
                                        
                                        {{-- THÊM PHẦN CHỌN SAO VÀO ĐÂY --}}
                                        <div class="mb-3 text-center">
                                            <label class="form-label d-block fw-bold">Thay đổi đánh giá (nếu muốn)</label>
                                            <p class="small text-muted">Đánh giá hiện tại của bạn là {{ $rating }} sao. Bạn có thể chọn lại.</p>
                                            <div class="rating-stars">
                                                @foreach(range(1, 5) as $star)
                                                    <i class="fas fa-star" 
                                                       wire:click="$set('rating', {{ $star }})" 
                                                       style="font-size: 2rem; cursor: pointer; color: {{ $rating >= $star ? '#ffc107' : '#e0e0e0' }}; transition: color 0.2s;">
                                                    </i>
                                                @endforeach
                                            </div>
                                            @error('rating') <span class="text-danger d-block mt-1">{{ $message }}</span> @enderror
                                        </div>
                                        {{-- KẾT THÚC PHẦN THÊM --}}

                                        <div class="mb-3">
                                            <label for="additionalFeedbackText" class="form-label fw-bold">Gửi phản hồi bổ sung</label>
                                            <textarea class="form-control @error('additionalFeedback') is-invalid @enderror" id="additionalFeedbackText" rows="4" wire:model.defer="additionalFeedback" placeholder="Gửi phản hồi của bạn về trả lời của người bán (tối thiểu 10 ký tự)"></textarea>
                                            @error('additionalFeedback') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                        </div>
                                        <button type="submit" class="btn btn-danger w-100" wire:loading.attr="disabled" wire:target="submitAdditionalFeedback">
                                            <span wire:loading.remove wire:target="submitAdditionalFeedback">
                                                GỬI PHẢN HỒI VÀ CẬP NHẬT
                                            </span>
                                            <span wire:loading wire:target="submitAdditionalFeedback">
                                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                Đang gửi...
                                            </span>
                                        </button>
                                    </form>
                                    {{-- ============================================== --}}
                                    {{-- KẾT THÚC CẬP NHẬT FORM --}}
                                    {{-- ============================================== --}}
                                @endif

                            @else
                                {{-- Seller chưa phản hồi (Giữ nguyên) --}}
                                <div class="alert alert-warning text-center mt-3">
                                    <i class="fas fa-clock me-1"></i>
                                    Người bán chưa phản hồi đánh giá này.
                                </div>
                            @endif


                        @else
                            {{-- =========================================== --}}
                            {{-- CASE 1: CHƯA ĐÁNH GIÁ (Giữ nguyên) --}}
                            {{-- =========================================== --}}
                            <form wire:submit.prevent="submitReview">
                                {{-- Đánh giá chung (Sao) --}}
                                <div class="mb-3 text-center">
                                    <label class="form-label d-block">Đánh giá chung</label>
                                    <div class="rating-stars">
                                        @foreach(range(1, 5) as $star)
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
                        @endif
                    
                    @else
                        {{-- Lỗi không tìm thấy sản phẩm --}}
                        <div class="alert alert-danger">Đã có lỗi xảy ra. Vui lòng thử lại.</div>
                    @endif
                </div>
                {{-- KẾT THÚC NỘI DUNG MODAL --}}

            </div>
        </div>
    </div>
    {{-- Lớp phủ (backdrop) cho modal --}}
    <div class="modal-backdrop fade show"></div>
    @endif
</div>