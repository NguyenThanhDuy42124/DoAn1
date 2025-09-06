<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Hồ Sơ Cá Nhân </title>
    <style>
        :root {
            --primary: #0066cc;
            --secondary: #ff6600;
            --light-bg: #f8f9fa;
            --dark-text: #333;
            --border-radius: 12px;
        }
        
        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            padding-top: 20px;
            padding-bottom: 50px;
        }
        
        .profile-container {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .profile-header {
            background: linear-gradient(to right, var(--primary), #004d99);
            color: white;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            padding: 25px;
            position: relative;
        }
        
        .profile-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .store-logo {
            font-size: 24px;
            font-weight: bold;
            color: white;
            display: inline-block;
            background: var(--secondary);
            padding: 5px 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 5px solid white;
            object-fit: cover;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .profile-body {
            padding: 30px;
        }
        
        .nav-pills .nav-link.active {
            background-color: var(--primary);
            color: white;
        }
        
        .nav-pills .nav-link {
            color: var(--dark-text);
            border-radius: 6px;
            margin-bottom: 5px;
        }
        
        .nav-pills .nav-link:hover {
            background-color: #e9ecef;
        }
        
        .section-title {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eaeaea;
        }
        
        .info-item {
            margin-bottom: 15px;
            display: flex;
        }
        
        .info-label {
            font-weight: 500;
            color: var(--dark-text);
            min-width: 150px;
        }
        
        .info-value {
            color: #6c757d;
        }
        
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .btn-outline-primary {
            color: var(--primary);
            border-color: var(--primary);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary);
            color: white;
        }
        
        .order-card {
            border: 1px solid #eaeaea;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s;
        }
        
        .order-card:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
        
        .order-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .status-delivered {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-shipping {
            background-color: #cce5ff;
            color: #004085;
        }
        
        .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .wishlist-item {
            padding: 15px;
            border: 1px solid #eaeaea;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        
        .badge-primary {
            background-color: var(--primary);
        }
        
        .notification-item {
            padding: 15px;
            border-left: 4px solid var(--primary);
            background-color: #f8f9fa;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        
        .notification-unread {
            background-color: #e8f4ff;
            border-left: 4px solid var(--secondary);
        }
        
        @media (max-width: 768px) {
            .info-item {
                flex-direction: column;
            }
            
            .info-label {
                min-width: auto;
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    @extends('layouts.app')
    @section('content')
         <div class="container profile-container">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="store-logo">ten shop</div>
            <div>
                <a href="/" class="btn btn-outline-primary mr-2"><i class="fas fa-home"></i> Trang chủ</a>
                <a href="/logout" class="btn btn-primary"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
            </div>
        </div>
        
        <!-- Profile Card -->
        <div class="profile-card">
            <div class="profile-header">
                <div class="d-flex align-items-center">
                    <img src="https://ui-avatars.com/api/?name=Nguyễn+Văn+A&background=ff6600&color=fff&size=120" alt="Avatar" class="profile-avatar mr-4">
                    <div>
                        <h2 class="mb-1">...</h2>
                        <p class="mb-0">Thành viên từ: ...</p>
                    </div>
                </div>
            </div>
            
            <div class="profile-body">
                <div class="row">
                    <!-- Sidebar menu -->
                    <div class="col-md-3">
                        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <a class="nav-link active" id="v-pills-home-tab" data-toggle="pill" href="#v-pills-home" role="tab" aria-controls="v-pills-home" aria-selected="true">
                                <i class="fas fa-user-circle mr-2"></i> Thông tin cá nhân
                            </a>
                            <a class="nav-link" id="v-pills-orders-tab" data-toggle="pill" href="#v-pills-orders" role="tab" aria-controls="v-pills-orders" aria-selected="false">
                                <i class="fas fa-shopping-bag mr-2"></i> Đơn hàng
                            </a>
                            <a class="nav-link" id="v-pills-wishlist-tab" data-toggle="pill" href="#v-pills-wishlist" role="tab" aria-controls="v-pills-wishlist" aria-selected="false">
                                <i class="fas fa-heart mr-2"></i> Sản phẩm yêu thích
                            </a>
                            <a class="nav-link" id="v-pills-address-tab" data-toggle="pill" href="#v-pills-address" role="tab" aria-controls="v-pills-address" aria-selected="false">
                                <i class="fas fa-map-marker-alt mr-2"></i> Sổ địa chỉ
                            </a>
                            <a class="nav-link" id="v-pills-notifications-tab" data-toggle="pill" href="#v-pills-notifications" role="tab" aria-controls="v-pills-notifications" aria-selected="false">
                                <i class="fas fa-bell mr-2"></i> Thông báo
                            </a>
                            <a class="nav-link" id="v-pills-security-tab" data-toggle="pill" href="#v-pills-security" role="tab" aria-controls="v-pills-security" aria-selected="false">
                                <i class="fas fa-shield-alt mr-2"></i> Bảo mật
                            </a>
                        </div>
                    </div>
                    
                    <!-- Content -->
                    <div class="col-md-9">
                        <div class="tab-content" id="v-pills-tabContent">
                            <!-- Thông tin cá nhân -->
                            <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">
                                <h4 class="section-title">Thông tin cá nhân</h4>
                                
                                <div class="info-item">
                                    <div class="info-label">Họ và tên:</div>
                                    <div class="info-value">...</div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label">Email:</div>
                                    <div class="info-value">...</div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label">Số điện thoại:</div>
                                    <div class="info-value">...</div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label">Ngày sinh:</div>
                                    <div class="info-value">...</div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label">Giới tính:</div>
                                    <div class="info-value">...</div>
                                </div>
                                
                                <button class="btn btn-primary mt-3"><i class="fas fa-edit mr-2"></i> Chỉnh sửa thông tin</button>
                            </div>
                            
                            <!-- Đơn hàng -->
                            <div class="tab-pane fade" id="v-pills-orders" role="tabpanel" aria-labelledby="v-pills-orders-tab">
                                <h4 class="section-title">Lịch sử đơn hàng</h4>
                                <!--
                                <div class="order-card">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="font-weight-bold">Mã đơn: #DH12345</div>
                                        <div class="order-status status-delivered">Đã giao</div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>Ngày đặt: 20/09/2023</div>
                                        <div class="font-weight-bold text-primary">12.990.000₫</div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <img src="https://images.unsplash.com/photo-1598327105666-5b89351aff97?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80" alt="Product" class="product-img mr-3">
                                        <div>
                                            <div class="font-weight-bold">iPhone 14 Pro Max 128GB</div>
                                            <div class="text-muted">Số lượng: 1</div>
                                        </div>
                                    </div>
                                    <div class="text-right mt-3">
                                        <button class="btn btn-outline-primary btn-sm">Xem chi tiết</button>
                                        <button class="btn btn-primary btn-sm">Mua lại</button>
                                    </div>
                                </div>
                                
                                <div class="order-card">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="font-weight-bold">Mã đơn: #DH12346</div>
                                        <div class="order-status status-shipping">Đang giao</div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>Ngày đặt: 05/10/2023</div>
                                        <div class="font-weight-bold text-primary">8.490.000₫</div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <img src="https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80" alt="Product" class="product-img mr-3">
                                        <div>
                                            <div class="font-weight-bold">Samsung Galaxy S23 Ultra</div>
                                            <div class="text-muted">Số lượng: 1</div>
                                        </div>
                                    </div>
                                    <div class="text-right mt-3">
                                        <button class="btn btn-outline-primary btn-sm">Theo dõi đơn hàng</button>
                                    </div>
                                </div>
                            -->
                                <div class="text-center mt-4">
                                    <button class="btn btn-outline-primary">Xem tất cả đơn hàng</button>
                                </div>
                            </div>
                            
                            <!-- Sản phẩm yêu thích -->
                            <div class="tab-pane fade" id="v-pills-wishlist" role="tabpanel" aria-labelledby="v-pills-wishlist-tab">
                                <h4 class="section-title">Sản phẩm yêu thích</h4>
                              <!--  
                                <div class="wishlist-item">
                                    <div class="row">
                                        <div class="col-3 col-md-2">
                                            <img src="https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80" alt="Product" class="img-fluid rounded">
                                        </div>
                                        <div class="col-9 col-md-7">
                                            <h5>Samsung Galaxy S23 Ultra</h5>
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="text-warning mr-2">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star-half-alt"></i>
                                                </div>
                                                <span class="text-muted">(128 đánh giá)</span>
                                            </div>
                                            <div class="text-success font-weight-bold">21.990.000₫</div>
                                        </div>
                                        <div class="col-12 col-md-3 mt-3 mt-md-0 text-right">
                                            <button class="btn btn-primary btn-block">Thêm vào giỏ</button>
                                            <button class="btn btn-outline-danger btn-block mt-2"><i class="fas fa-trash"></i> Xóa</button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="wishlist-item">
                                    <div class="row">
                                        <div class="col-3 col-md-2">
                                            <img src="https://images.unsplash.com/photo-1598327105666-5b89351aff97?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80" alt="Product" class="img-fluid rounded">
                                        </div>
                                        <div class="col-9 col-md-7">
                                            <h5>iPhone 14 Pro Max 128GB</h5>
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="text-warning mr-2">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                </div>
                                                <span class="text-muted">(256 đánh giá)</span>
                                            </div>
                                            <div class="text-success font-weight-bold">28.990.000₫</div>
                                        </div>
                                        <div class="col-12 col-md-3 mt-3 mt-md-0 text-right">
                                            <button class="btn btn-primary btn-block">Thêm vào giỏ</button>
                                            <button class="btn btn-outline-danger btn-block mt-2"><i class="fas fa-trash"></i> Xóa</button>
                                        </div>
                                    </div>
                                </div>
                            --> 
                                <div class="text-center mt-4">
                                    <button class="btn btn-outline-primary">Xem thêm sản phẩm</button>
                                </div>
                            </div>
                            
                            <!-- Các tab khác có thể được thêm ở đây -->
                            <div class="tab-pane fade" id="v-pills-address" role="tabpanel" aria-labelledby="v-pills-address-tab">
                                <h4 class="section-title">Sổ địa chỉ</h4>
                                <p class="text-muted">...</p>
                            </div>
                            
                            <div class="tab-pane fade" id="v-pills-notifications" role="tabpanel" aria-labelledby="v-pills-notifications-tab">
                                <h4 class="section-title">Thông báo</h4>
                                <p class="text-muted">...</p>
                            </div>
                            
                            <div class="tab-pane fade" id="v-pills-security" role="tabpanel" aria-labelledby="v-pills-security-tab">
                                <h4 class="section-title">Bảo mật tài khoản</h4>
                                <p class="text-muted">...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Points & Vouchers -->
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="profile-card h-100">
                    <div class="profile-body">
                        <h4 class="section-title">Điểm tích lũy</h4>
                        <div class="text-center py-4">
                            <div class="display-4 text-primary font-weight-bold">...</div>
                            <p class="text-muted">Điểm hiện có</p>
                            <button class="btn btn-outline-primary">Đổi điểm ngay</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-3">
                <div class="profile-card h-100">
                    <div class="profile-body">
                        <h4 class="section-title">Voucher của tôi</h4>
                        <div class="text-center py-4">
                            <div class="display-4 text-primary font-weight-bold">...</div>
                            <p class="text-muted">Voucher đang có</p>
                            <button class="btn btn-outline-primary">Xem voucher</button>
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
</body>
</html>