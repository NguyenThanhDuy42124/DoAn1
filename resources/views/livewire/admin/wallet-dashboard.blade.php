<style>
    /* --- BẢNG MÀU TÙY CHỈNH (Tối ưu độ tương phản trên nền trắng) --- */

    /* 1. Vàng (Warning) -> Chuyển sang Vàng Cam Đậm */
    .text-warning-dark {
        color: #b45309 !important;
    }

    .bg-warning-subtle-custom {
        background-color: #fffbeb;
        color: #b45309;
    }

    .border-warning-dark {
        border-color: #f59e0b !important;
    }

    /* 2. Tím (Purple) -> Tím Đậm */
    .text-purple-dark {
        color: #7e22ce !important;
    }

    .bg-purple-subtle-custom {
        background-color: #f3e8ff;
        color: #7e22ce;
    }

    .border-purple {
        border-color: #a855f7 !important;
    }

    /* 3. Xanh lá (Success) -> Xanh Dương Đậm (Dễ đọc hơn) */
    .text-success-dark {
        color: #0ea5e9 !important;
    }

    /* Đổi từ xanh lá sang xanh dương sáng */
    .text-success-content {
        color: #0369a1 !important;
    }

    /* Màu chữ nội dung đậm hơn */
    .bg-success-subtle-custom {
        background-color: #e0f2fe;
        color: #0369a1;
    }

    /* Nền xanh dương nhạt */

    /* 4. Xanh dương (Primary) -> Xanh Lá Cây Đậm (Dễ đọc) */
    .text-primary-dark {
        color: #059669 !important;
    }

    /* Đổi từ xanh dương sang xanh lá đậm */
    .text-primary-content {
        color: #047857 !important;
    }

    /* Màu chữ nội dung */
    .bg-primary-subtle-custom {
        background-color: #d1fae5;
        color: #047857;
    }

    /* Nền xanh lá nhạt */

    /* --- TIỆN ÍCH KHÁC --- */
    /* Scrollbar đẹp */
    .table-responsive::-webkit-scrollbar {
        height: 6px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    /* Hover Effect */
    .stats-card {
        transition: all 0.2s ease-in-out;
    }

    .stats-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
    }

</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 fw-bold text-dark mb-0">
            <i class="bi bi-wallet2 me-2 text-primary-dark"></i>Quản Lý Ví Hệ Thống
        </h1>
        <span class="badge bg-white text-secondary border fw-normal shadow-sm">
            <i class="bi bi-clock me-1"></i>{{ now()->format('H:i d/m/Y') }}
        </span>
    </div>

    <div class="row g-3 mb-4">

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card stats-card shadow-sm h-100 border-0 border-start border-4 border-warning-dark">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class=" small fw-bold text-uppercase">Tiền Tạm Giữ</div>
                        <div class="" style="width: 36px; height: 36px;">
                            <i class=""></i>
                        </div>
                    </div>
                    <div class="h4 fw-bold text-dark mb-1">
                        {{ number_format($StripeIncoming, 0, ',', '.') }} <span class="fs-6  fw-normal">đ</span>
                    </div>
                    <div class="d-flex align-items-center small ">
                        <i class="bi bi-info-circle me-1"></i> Chờ khách nhận hàng
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card stats-card shadow-sm h-100 border-0 border-start border-4" style="border-color: #0ea5e9 !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class=" small fw-bold text-uppercase">Doanh Thu Sàn</div>
                        <div class="" style="width: 36px; height: 36px;">
                            <i class=""></i>
                        </div>
                    </div>
                    <div class="h4 fw-bold text-dark mb-1">
                        {{ number_format($realIncome, 0, ',', '.') }} <span class="fs-6  fw-normal">đ</span>
                    </div>
                    <div class="d-flex align-items-center small ">
                        <i class="bi bi-info-circle me-1"></i> Lợi nhuận thực tế
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card stats-card shadow-sm h-100 border-0 border-start border-4" style="border-color: #059669 !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class=" small fw-bold text-uppercase">Phải Trả Seller</div>
                        <div class="" style="width: 36px; height: 36px;">
                            <i class=""></i>
                        </div>
                    </div>
                    <div class="h4 fw-bold text-dark mb-1">
                        {{ number_format($totalLoanSeller, 0, ',', '.') }} <span class="fs-6 fw-normal">đ</span>
                    </div>
                    <div class="d-flex align-items-center small ">
                        <i class="bi bi-info-circle me-1"></i> Ví người bán
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card stats-card shadow-sm h-100 border-0 border-start border-4 border-purple">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class=" small fw-bold text-uppercase">Số Dư Stripe</div>
                        <div class="" style="width: 36px; height: 36px;">
                            <i class=""></i>
                        </div>
                    </div>
                    <div class="h4 fw-bold text-dark mb-1">
                        {{ number_format($Stripebalance, 0, ',', '.') }} <span class="fs-6  fw-normal">đ</span>
                    </div>
                    <div class="d-flex align-items-center small ">
                        <i class="bi bi-info-circle me-1"></i> Thực tế
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                <i class="bi bi-clock-history me-2 text-secondary"></i>Lịch sử giao dịch
            </h5>
            <button class="btn btn-sm btn-light text-secondary border">
                <i class="bi bi-arrows-expand me-1"></i>Mở rộng
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-sm">
                <thead class="bg-light">
                    <tr class="text-uppercase text-secondary small" style="font-size: 0.75rem;">
                        <th class="py-3 ps-4 fw-bold">Thời gian / Ref</th>
                        <th class="py-3 fw-bold">Người mua</th>
                        <th class="py-3 fw-bold">Người bán</th>
                        <th class="py-3 text-end fw-bold">Tổng tiền</th>
                        <th class="py-3 fw-bold">Mã GD</th>
                        <th class="py-3 fw-bold">Phí GD</th>
                        <th class="py-3 fw-bold">Trạng thái</th>
                        <th class="py-3 fw-bold">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex flex-column">
                                <span class="fw-medium text-dark">
                                    {{ $order->created_at->format('d/m/Y') }}
                                    <span class="text-muted fw-normal ms-1">{{ $order->created_at->format('H:i') }}</span>
                                </span>
                                <span class="text-muted small font-monospace">#{{ $order->id }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle bg-light text-secondary rounded-circle d-flex align-items-center justify-content-center me-2 border" style="width: 32px; height: 32px; font-weight: 600;">
                                    {{ substr($order->buyer->name ?? 'U', 0, 1) }}
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark small">{{ $order->buyer->name ?? 'Unknown' }}</span>
                                    <span class="text-muted" style="font-size: 0.7rem;">{{ $order->buyer->email ?? '' }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle bg-light text-secondary rounded-circle d-flex align-items-center justify-content-center me-2 border" style="width: 32px; height: 32px; font-weight: 600;">
                                    {{ substr($order->seller->name ?? 'S', 0, 1) }}
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark small">{{ $order->seller->name ?? 'Unknown' }}</span>
                                    <span class="text-muted" style="font-size: 0.7rem;">{{ $order->seller->email ?? '' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="text-end">
                            <span class="fw-bold text-dark">{{ number_format($order->total_price, 0, ',', '.') }}</span>
                        </td>
                        <td>
                            <span class="text-secondary small font-monospace">{{ $order->transaction_id ?? 'N/A' }}</span>
                        </td>
                        <td>
                            <span class="text-secondary small font-monospace">{{ $order->transaction_fee ? number_format($order->transaction_fee, 0, ',', '.') : '0' }}</span>
                        </td>
                        <td>
                            @if($order->status == 'Pending')
                            <span class="badge bg-warning-subtle-custom text-warning-dark fw-medium">Chờ xử lý</span>
                            @elseif($order->status == 'Completed')
                            <span class="badge bg-success-subtle-custom text-success-dark fw-medium">Hoàn thành</span>
                            @elseif($order->status == 'Shipped')
                            <span class="badge bg-primary-subtle-custom text-primary-dark fw-medium">Đang giao hàng</span>
                            @else
                            <span class="badge bg-secondary fw-medium text-white">Khác</span>
                            @endif
                        </td>
                        <td>
                            @if($order->status =='Completed')
                            <button class="btn btn-sm btn-primary">chuyển tiền cho seller (đang phát triển)</button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
        <div class="card-footer bg-white border-top py-3">
            <div class="d-flex justify-content-end">
                {{ $orders->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @endif

    </div>
