@extends('layouts.SellerDashBoard')
@section('content')
<div class="container">
    
    <div class="page-header mt-4 mb-4">
        <h1 class="h2">Tổng quan</h1>
        <p class="text-muted">Chào mừng trở lại, đây là những gì đang diễn ra hôm nay.</p>
    </div>

    <div class="row g-4 mb-4">
        
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm h-100 border-start border-primary border-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-muted text-uppercase small fw-bold">Doanh thu (Hôm nay)</div>
                            <h3 class="fw-bold mb-0 mt-2">...</h3>
                        </div>
                        <div class="fs-2 text-primary">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm h-100 border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-muted text-uppercase small fw-bold">Đơn hàng (Hôm nay)</div>
                            <h3 class="fw-bold mb-0 mt-2">...</h3>
                        </div>
                        <div class="fs-2 text-success">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

       <div class="col-md-6 col-lg-3">
    <div class="card shadow-sm h-100 border-start border-info border-4">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <div class="text-muted text-uppercase small fw-bold">Sản phẩm đang bán</div>
                    
                    <h3 class="fw-bold mb-0 mt-2">{{ $approvedProductCount }}</h3>
                    
                </div>
                <div class="fs-2 text-info">
                    <i class="fas fa-box"></i>
                </div>
            </div>
        </div>
    </div>
</div>
        <div class="col-md-6 col-lg-3">
    <div class="card shadow-sm h-100 border-start border-warning border-4">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <div class="text-muted text-uppercase small fw-bold">Sản phẩm chờ duyệt</div>
                    
                    <h3 class="fw-bold mb-0 mt-2">{{ $pendingProductCount }}</h3>
                    
                </div>
                <div class="fs-2 text-warning">
                    <i class="fas fa-pause-circle"></i>
                </div>
            </div>
        </div>
    </div>
</div>
    </div>

    <div class="row g-4">
        
        <div class="col-lg-8">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="fas fa-chart-bar me-2"></i>Báo cáo doanh thu (7 ngày qua)</h5>
                    <a href="#" class="btn btn-sm btn-outline-primary">Xem chi tiết</a>
                </div>
                <div class="card-body">
                    <div style="height: 350px; background: #f8f9fa; display: grid; place-items: center; border-radius: 5px;">
                        <span class="text-muted">[Biểu đồ cột sẽ hiển thị ở đây]</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-list-ul me-2"></i>Đơn hàng mới nhất</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Đơn hàng x</h6>
                                <small class="text-muted">5 phút trước</small>
                            </div>
                            <p class="mb-1 fw-bold text-success">Giá y</p>
                            <small>Người mua z</small>
                        </a>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="#">Xem tất cả đơn hàng</a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection