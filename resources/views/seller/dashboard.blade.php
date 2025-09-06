<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard Seller - TechStore</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    @vite(['resources/css/dashboard_seller.css', 'resources/js/app.js'])
</head>
<body>
    @extends('layouts.app')
    @section('content')
    <div class="container dashboard-container">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="store-logo">SELLER Ở ĐÂY NÈ</div>
            <div>
                <a href="/" class="btn btn-outline-primary mr-2"><i class="fas fa-home"></i> Trang chủ</a>
                <a href="/logout" class="btn btn-primary"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
            </div>
        </div>
        
        <!-- Dashboard Card -->
        <div class="dashboard-card">
            <div class="dashboard-header">
                <div class="d-flex align-items-center">
                    <img src="https://ui-avatars.com/api/?name=Nguyễn+Văn+B&background=ff6600&color=fff&size=120" alt="Avatar" class="seller-avatar mr-4">
                    <div>
                        <h2 class="mb-1">... - Seller</h2>
                        <p class="mb-0">Nhà bán hàng từ: ../../....</p>
                        <p class="mb-0">Đánh giá: ../.. (... đánh giá)</p>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-body">
                <div class="row">
                    <!-- Sidebar menu -->
                    <div class="col-md-3">
                        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <a class="nav-link active" id="v-pills-dashboard-tab" data-toggle="pill" href="#v-pills-dashboard" role="tab" aria-controls="v-pills-dashboard" aria-selected="true">
                                <i class="fas fa-tachometer-alt mr-2"></i> Tổng quan
                            </a>
                            <a class="nav-link" id="v-pills-orders-tab" data-toggle="pill" href="#v-pills-orders" role="tab" aria-controls="v-pills-orders" aria-selected="false">
                                <i class="fas fa-shopping-bag mr-2"></i> Đơn hàng
                            </a>
                            <a class="nav-link" id="v-pills-products-tab" data-toggle="pill" href="#v-pills-products" role="tab" aria-controls="v-pills-products" aria-selected="false">
                                <i class="fas fa-box mr-2"></i> Sản phẩm
                            </a>
                            <a class="nav-link" id="v-pills-analytics-tab" data-toggle="pill" href="#v-pills-analytics" role="tab" aria-controls="v-pills-analytics" aria-selected="false">
                                <i class="fas fa-chart-line mr-2"></i> Thống kê
                            </a>
                            <a class="nav-link" id="v-pills-customers-tab" data-toggle="pill" href="#v-pills-customers" role="tab" aria-controls="v-pills-customers" aria-selected="false">
                                <i class="fas fa-users mr-2"></i> Khách hàng
                            </a>
                            <a class="nav-link" id="v-pills-promotions-tab" data-toggle="pill" href="#v-pills-promotions" role="tab" aria-controls="v-pills-promotions" aria-selected="false">
                                <i class="fas fa-percent mr-2"></i> Khuyến mãi
                            </a>
                            <a class="nav-link" id="v-pills-settings-tab" data-toggle="pill" href="#v-pills-settings" role="tab" aria-controls="v-pills-settings" aria-selected="false">
                                <i class="fas fa-cog mr-2"></i> Cài đặt
                            </a>
                        </div>
                    </div>
                    
                    <!-- Content -->
                    <div class="col-md-9">
                        <div class="tab-content" id="v-pills-tabContent">
                            <!-- Tổng quan -->
                            <div class="tab-pane fade show active" id="v-pills-dashboard" role="tabpanel" aria-labelledby="v-pills-dashboard-tab">
                                <h4 class="section-title">Tổng quan cửa hàng</h4>
                                
                                <div class="row mb-4">
                                    <div class="col-md-3">
                                        <div class="stats-card">
                                            <div class="stats-icon">
                                                <i class="fas fa-shopping-bag"></i>
                                            </div>
                                            <div class="stats-value">...</div>
                                            <div class="stats-label">Đơn hàng</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stats-card">
                                            <div class="stats-icon">
                                                <i class="fas fa-box"></i>
                                            </div>
                                            <div class="stats-value">...</div>
                                            <div class="stats-label">Sản phẩm</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stats-card">
                                            <div class="stats-icon">
                                                <i class="fas fa-users"></i>
                                            </div>
                                            <div class="stats-value">...</div>
                                            <div class="stats-label">Khách hàng</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stats-card">
                                            <div class="stats-icon">
                                                <i class="fas fa-money-bill-wave"></i>
                                            </div>
                                            <div class="stats-value">...</div>
                                            <div class="stats-label">Doanh thu</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="dashboard-card">
                                            <div class="dashboard-body">
                                                <h5 class="section-title">Doanh thu</h5>
                                                <div class="chart-container">
                                                    <canvas id="revenueChart"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="dashboard-card">
                                            <div class="dashboard-body">
                                                <h5 class="section-title">Trạng thái đơn hàng</h5>
                                                <div class="chart-container">
                                                    <canvas id="orderStatusChart"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="dashboard-card mt-4">
                                    <div class="dashboard-body">
                                        <h5 class="section-title">Đơn hàng mới nhất</h5>
                                     <!--   
                                        <div class="order-card">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div class="font-weight-bold">Mã đơn: #DH12347</div>
                                                <div class="order-status status-pending">Chờ xác nhận</div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div>Ngày đặt: 12/10/2023</div>
                                                <div class="font-weight-bold text-primary">5.990.000₫</div>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <img src="https://images.unsplash.com/photo-1598327105666-5b89351aff97?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80" alt="Product" class="product-img mr-3">
                                                <div>
                                                    <div class="font-weight-bold">iPhone 14 Pro Max 128GB</div>
                                                    <div class="text-muted">Số lượng: 1</div>
                                                </div>
                                            </div>
                                            <div class="text-right mt-3">
                                                <button class="btn btn-outline-primary btn-sm mr-2">Xem chi tiết</button>
                                                <button class="btn btn-primary btn-sm">Xác nhận</button>
                                            </div>
                                        </div>
                                    -->  
                                        <div class="text-center mt-3">
                                            <button class="btn btn-outline-primary">Xem tất cả đơn hàng</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Đơn hàng -->
                            <div class="tab-pane fade" id="v-pills-orders" role="tabpanel" aria-labelledby="v-pills-orders-tab">
                                <h4 class="section-title">Quản lý đơn hàng</h4>
                                <p class="text-muted">...</p>
                            </div>
                            
                            <!-- Sản phẩm -->
                            <div class="tab-pane fade" id="v-pills-products" role="tabpanel" aria-labelledby="v-pills-products-tab">
                                <h4 class="section-title">Quản lý sản phẩm</h4>
                                <p class="text-muted">...</p>
                            </div>
                            
                            <!-- Các tab khác có thể được thêm ở đây -->
                            <div class="tab-pane fade" id="v-pills-analytics" role="tabpanel" aria-labelledby="v-pills-analytics-tab">
                                <h4 class="section-title">Thống kê và báo cáo</h4>
                                <p class="text-muted">...</p>
                            </div>
                            
                            <div class="tab-pane fade" id="v-pills-customers" role="tabpanel" aria-labelledby="v-pills-customers-tab">
                                <h4 class="section-title">Quản lý khách hàng</h4>
                                <p class="text-muted">...</p>
                            </div>
                            
                            <div class="tab-pane fade" id="v-pills-promotions" role="tabpanel" aria-labelledby="v-pills-promotions-tab">
                                <h4 class="section-title">Khuyến mãi và giảm giá</h4>
                                <p class="text-muted">...</p>
                            </div>
                            
                            <div class="tab-pane fade" id="v-pills-settings" role="tabpanel" aria-labelledby="v-pills-settings-tab">
                                <h4 class="section-title">Cài đặt cửa hàng</h4>
                                <p class="text-muted">...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
</body>
</html>