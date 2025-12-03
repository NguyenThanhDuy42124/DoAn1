<style>
    /* --- BẢNG MÀU TÙY CHỈNH (Tối ưu độ tương phản trên nền trắng) --- */
    
    /* 1. Vàng (Warning) -> Chuyển sang Vàng Cam Đậm */
    .text-warning-dark { color: #b45309 !important; } 
    .bg-warning-subtle-custom { background-color: #fffbeb; color: #b45309; }
    .border-warning-dark { border-color: #f59e0b !important; }

    /* 2. Tím (Purple) -> Tím Đậm */
    .text-purple-dark { color: #7e22ce !important; }
    .bg-purple-subtle-custom { background-color: #f3e8ff; color: #7e22ce; }
    .border-purple { border-color: #a855f7 !important; }

    /* 3. Xanh lá (Success) -> Xanh Dương Đậm (Dễ đọc hơn) */
    .text-success-dark { color: #0ea5e9 !important; } /* Đổi từ xanh lá sang xanh dương sáng */
    .text-success-content { color: #0369a1 !important; } /* Màu chữ nội dung đậm hơn */
    .bg-success-subtle-custom { background-color: #e0f2fe; color: #0369a1; } /* Nền xanh dương nhạt */
    
    /* 4. Xanh dương (Primary) -> Xanh Lá Cây Đậm (Dễ đọc) */
    .text-primary-dark { color: #059669 !important; } /* Đổi từ xanh dương sang xanh lá đậm */
    .text-primary-content { color: #047857 !important; } /* Màu chữ nội dung */
    .bg-primary-subtle-custom { background-color: #d1fae5; color: #047857; } /* Nền xanh lá nhạt */

    /* --- TIỆN ÍCH KHÁC --- */
    /* Scrollbar đẹp */
    .table-responsive::-webkit-scrollbar { height: 6px; }
    .table-responsive::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
    
    /* Hover Effect */
    .stats-card { transition: all 0.2s ease-in-out; }
    .stats-card:hover { transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important; }
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
                        {{ number_format($systemHoldingBalance, 0, ',', '.') }} <span class="fs-6  fw-normal">đ</span>
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
                        {{ number_format($adminBalance, 0, ',', '.') }} <span class="fs-6  fw-normal">đ</span>
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
                        {{ number_format($totalSellerBalance, 0, ',', '.') }} <span class="fs-6 fw-normal">đ</span>
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
                        {{ number_format($testballace, 0, ',', '.') }} <span class="fs-6  fw-normal">đ</span>
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
                        <th class="py-3 fw-bold">User</th>
                        <th class="py-3 fw-bold">Loại GD</th>
                        <th class="py-3 text-end fw-bold">Số tiền</th>
                        <th class="py-3 pe-4 fw-bold">Nội dung</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $tx)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex flex-column">
                                <span class="fw-medium text-dark">{{ $tx->created_at->format('d/m/Y') }} <span class="text-muted fw-normal ms-1">{{ $tx->created_at->format('H:i') }}</span></span>
                                <span class="text-muted small font-monospace">#{{ $tx->reference_id ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle bg-light text-secondary rounded-circle d-flex align-items-center justify-content-center me-2 border" style="width: 32px; height: 32px; font-weight: 600;">
                                    {{ substr($tx->wallet->user->name ?? 'U', 0, 1) }}
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark small">{{ $tx->wallet->user->name ?? 'Unknown' }}</span>
                                    <span class="text-muted" style="font-size: 0.7rem;">{{ $tx->wallet->user->role }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            @php
                                $badgeClass = match($tx->type) {
                                    'commission' => 'bg-success-subtle-custom text-success-dark border-success-subtle-custom',
                                    'deposit' => 'bg-primary-subtle-custom text-primary-dark border-primary-subtle-custom',
                                    'withdraw' => 'bg-danger-subtle text-danger border-danger-subtle',
                                    default => 'bg-secondary-subtle text-secondary border-secondary-subtle'
                                };
                                $typeName = match($tx->type) {
                                    'commission' => 'Hoa hồng',
                                    'deposit' => 'Nạp tiền',
                                    'withdraw' => 'Rút tiền',
                                    default => ucfirst($tx->type)
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }} border rounded-pill px-2 py-1 fw-normal">
                                {{ $typeName }}
                            </span>
                        </td>
                        <td class="text-end">
                            <span class="fw-bold {{ $tx->amount > 0 ? 'text-success-dark' : 'text-danger' }}">
                                {{ $tx->amount > 0 ? '+' : '' }}{{ number_format($tx->amount, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="pe-4">
                            <div class="text-truncate text-secondary small" style="max-width: 250px;" title="{{ $tx->description }}">
                                {{ $tx->description }}
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
        <div class="card-footer bg-white border-top py-3">
            <div class="d-flex justify-content-end">
                {{ $transactions->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @endif
    </div>
</div>