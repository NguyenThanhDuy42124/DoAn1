<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    @vite(['resources/css/dashboard_seller.css', 'resources/js/app.js'])
     <title>Dashboard Seller </title>
</head>

<body>
 
    <div class="container dashboard-container">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="store-logo">USER Ở ĐÂY NÈ</div>
            <div class="d-flex align-items-center justify-content-between" style="width: 250px;">
                <a href="/" class="btn btn-outline-primary mr-2"><i class="fas fa-home"></i> Trang chủ</a>
                <div>
                <form action="/logout" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary fas fa-sign-out-alt" style="height: 40px">Đăng Xuất</button>
                </form>
                </div>
            </div>
        </div>

        <!-- Dashboard Card -->
        <div class="dashboard-card">
            <div class="dashboard-header">
                <div class="d-flex align-items-center">
                    <img src="https://ui-avatars.com/api/?name=Nguyễn+Văn+B&background=ff6600&color=fff&size=120"
                        alt="Avatar" class="seller-avatar mr-4">
                    <div>
                        <h2 class="mb-1">{{ Auth::user()->name }}</h2>
                        <p class="mb-0">Nhà bán hàng từ: {{ Auth::user()->created_at->format('d/m/Y') }}</p>
                        <p class="mb-0">Đánh giá: {{ Auth::user()->rating }} ({{ Auth::user()->reviews_count }} đánh giá)</p>
                    </div>
                </div>
            </div>

            <div class="dashboard-body">
                <div class="row">
                    <!-- Sidebar menu -->
                    <div class="col-md-3">
                        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist"
                            aria-orientation="vertical">
                            <a class="nav-link active" id="v-pills-dashboard-tab" data-toggle="pill"
                                href="#v-pills-dashboard" role="tab" aria-controls="v-pills-dashboard"
                                aria-selected="true">
                                <i class="fas fa-tachometer-alt mr-2"></i> Tổng quan
                            </a>
                            
                            <a class="nav-link" id="v-pills-settings-tab" data-toggle="pill" href="#v-pills-settings"
                                role="tab" aria-controls="v-pills-settings" aria-selected="false">
                                <i class="fas fa-cog mr-2"></i> Cài đặt
                            </a>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="col-md-9">
                        <div class="tab-content" id="v-pills-tabContent">
                            <!-- Tổng quan -->
                            <div class="tab-pane fade show active" id="v-pills-dashboard" role="tabpanel"
                                aria-labelledby="v-pills-dashboard-tab">
                                <h4 class="section-title">Tổng quan cửa hàng</h4>

                                <div class="row mb-4">
                                    <div class="col-md-3">
                                        <div class="stats-card">
                                            <div class="stats-icon">
                                                <i class="fas fa-shopping-bag"></i>
                                            </div>
                                            <div class="stats-label">Đơn hàng</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="{{ route('seller.products.index') }}" style="text-decoration:none; color:inherit;">
                                        <div class="stats-card">
                                            <div class="stats-icon">
                                                <i class="fas fa-box"></i>
                                            </div>
                                            <div class="stats-label">Sản phẩm</div>
                                        </div>
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stats-card">
                                            <div class="stats-icon">
                                                <i class="fas fa-users"></i>
                                            </div>
                                            <div class="stats-label">Khách hàng</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stats-card">
                                            <div class="stats-icon">
                                                <i class="fas fa-money-bill-wave"></i>
                                            </div>
                                            <div class="stats-label">Doanh thu</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="{{ route('vouchers.index') }}" style="text-decoration:none; color:inherit;">
                                            <div class="stats-card">
                                              <div class="stats-icon">
                                                  <i class="fas fa-percent mr-2"></i>
                                             </div>
                                            <div class="stats-label">Khuyến mãi</div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stats-card">
                                            <div class="stats-icon">
                                                <i class="fas fa-chart-line mr-2"></i>
                                            </div>
                                            <div class="stats-label">Thống kê</div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
   
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</body>

</html>