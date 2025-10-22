<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    @vite(['resources/css/dashboard_user.css', 'resources/js/app.js'])

</head>

<body>
    @extends('layouts.app')
    @section('title', 'Dashboard Buyer')
    @section('content')
    <div class="container profile-container">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="store-logo">USER</div>
            <div class="d-flex align-items-center justify-content-between" style="width: 250px;">
                <a href="/" class="btn btn-outline-primary mr-2"><i class="fas fa-home"></i> Trang chủ</a>
                <div>
                    <form action="/logout" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary fas fa-sign-out-alt" style="height: 40px">Đăng
                            Xuất</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Profile Card -->
        <div class="profile-card">
            <div class="profile-header">
                <div class="d-flex align-items-center">
                @if(Auth::user()->img == "")
                    <img src="{{ asset('storage/profile_images/default.jpg') }}" alt="none default Image" class="profile-avatar mr-4">
                @else
                    <img src="{{ asset('storage/' . Auth::user()->img) }}" alt="Profile Image" class="profile-avatar mr-4">
                @endif
                    <div>
                        <h2 class="mb-1">{{ Auth::user()->name }}</h2>
                        <p class="mb-0">Thành viên từ: {{ Auth::user()->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="profile-body">
                <div class="row">
                    <!-- Sidebar menu -->
                    <div class="col-md-3">
                        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist"
                            aria-orientation="vertical">
                            <a class="nav-link active" id="v-pills-home-tab" data-toggle="pill" href="#v-pills-home"
                                role="tab" aria-controls="v-pills-home" aria-selected="true">
                                <i class="fas fa-user-circle mr-2"></i> Thông tin cá nhân
                            </a>
                            <a class="nav-link" id="v-pills-orders-tab" data-toggle="pill" href="#v-pills-orders"
                                role="tab" aria-controls="v-pills-orders" aria-selected="false">
                                <i class="fas fa-shopping-bag mr-2"></i> Đơn hàng
                            </a>
                            <a class="nav-link" id="v-pills-wishlist-tab" data-toggle="pill" href="#v-pills-wishlist"
                                role="tab" aria-controls="v-pills-wishlist" aria-selected="false">
                                <i class="fas fa-heart mr-2"></i> Sản phẩm yêu thích
                            </a>
                            <a class="nav-link" id="v-pills-carts-tab" data-toggle="pill" href="#v-pills-carts"
                                role="tab" aria-controls="v-pills-carts" aria-selected="false">
                                <i class="fas fa-map-marker-alt mr-2"></i> Giỏ hàng
                            </a>
                            <a class="nav-link" id="v-pills-notifications-tab" data-toggle="pill"
                                href="#v-pills-notifications" role="tab" aria-controls="v-pills-notifications"
                                aria-selected="false">
                                <i class="fas fa-bell mr-2"></i> Thông báo
                            </a>
                            <a class="nav-link" id="v-pills-security-tab" data-toggle="pill" href="#v-pills-security"
                                role="tab" aria-controls="v-pills-security" aria-selected="false">
                                <i class="fas fa-shield-alt mr-2"></i> Bảo mật
                            </a>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="col-md-9">
                        <div class="tab-content" id="v-pills-tabContent">
                            <!-- Thông tin cá nhân -->
                            <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel"
                                aria-labelledby="v-pills-home-tab">
                                <h4 class="section-title">Thông tin cá nhân</h4>

                                <div class="info-item">
                                    <div class="info-label">Họ và tên:</div>
                                    <div class="info-value">{{ Auth::user()->name }}</div>
                                </div>

                                <div class="info-item">
                                    <div class="info-label">Email:</div>
                                    <div class="info-value">{{ Auth::user()->email }}</div>
                                </div>

                                <div class="info-item">
                                    <div class="info-label">Số điện thoại:</div>
                                    <div class="info-value">{{ Auth::user()->phoneNumber }}</div>
                                </div>

                                <div class="info-item">
                                    <div class="info-label">Ngày sinh:</div>
                                    <div class="info-value">{{ Auth::user()->dateOfBirth }}</div>
                                </div>

                                <div class="info-item">
                                    <div class="info-label">Giới tính:</div>
                                    <div class="info-value">{{ Auth::user()->gender }}</div>
                                </div>
                                @if(Auth::user()->role !== 'admin' && auth()->user()->address != null)
                                    <div class="info-item">
                                    <div class="info-label">Địa chỉ:</div>
                                    <div class="info-value">{{ Auth::user()->address }}</div>
                                </div>
                                @endif
                                <div class="info-item">
                                    <div class="info-label">Quyền hạn:</div>
                                    <div class="info-value"> {{ Auth::user()->role }}</div>
                                </div>
                                <div>
                                    @if(Auth::user()->role == 'seller')
                                    <a href="{{ route('switchRole', 'seller') }}" class="btn btn-primary">Chuyển sang
                                        Seller</a>
                                    @endif

                                    @if(Auth::user()->role == 'admin')
                                    <a href="{{ route('switchRole', 'admin') }}" class="btn btn-danger">Chuyển sang
                                        Admin</a>
                                    @endif

                                </div>
                                <div>
                                    @if(Auth::user()->role == 'admin')
                                    <a href="{{ route('users.edit', Auth::user()->id) }}" class="btn btn-primary">Chỉnh sửa thông tin</a>
                                    @elseif(Auth::user()->role == 'buyer' || Auth::user()->role == 'seller')
                                    <a href="{{ route('general.users.edit', ['id' => Auth::user()->id, 'absolute' => true]) }}" class="btn btn-primary">Chỉnh sửa thông tin</a>
                                    @endif
                                </div>


                            </div>

                            <!-- Đơn hàng -->
                            <div class="tab-pane fade" id="v-pills-orders" role="tabpanel"
                                aria-labelledby="v-pills-orders-tab">
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
                                    <a href="{{ route('buyer.orders.index') }}" class="btn btn-outline-primary">Xem tất cả đơn hàng</a>
                                </div>
                            </div>

                            <!-- Sản phẩm yêu thích -->
                            <div class="tab-pane fade" id="v-pills-wishlist" role="tabpanel"
                                aria-labelledby="v-pills-wishlist-tab">
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
                            <div class="tab-pane fade" id="v-pills-carts" role="tabpanel"
                                aria-labelledby="v-pills-carts-tab">
                                <h4 class="section-title">Giỏ hàng</h4>
                                <p class="text-muted">...</p>
                            </div>

                            <div class="tab-pane fade" id="v-pills-notifications" role="tabpanel"
                                aria-labelledby="v-pills-notifications-tab">
                                <h4 class="section-title">Thông báo</h4>
                                <p class="text-muted">...</p>
                            </div>

                            <div class="tab-pane fade" id="v-pills-security" role="tabpanel"
                                aria-labelledby="v-pills-security-tab">
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
    <button
    type="button"
    id="chat-bubble"
    class="btn btn-primary shadow rounded-circle"
    onclick="window.location.href='{{ route('chatify') }}'">
    <i class="bi bi-chat-dots-fill"></i>
    </button>
    
    @endsection



</body>

</html>
