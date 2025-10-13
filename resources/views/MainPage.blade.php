@extends('layouts.app')

@section('content')

<!-- 🌈 Banner chính -->
<section class="container-fluid px-0">
    <div id="mainCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="https://cdn.tgdd.vn/Files/2024/09/15/banner-iphone-16-1200x400.jpg" class="d-block w-100" alt="Banner 1">
            </div>
            <div class="carousel-item">
                <img src="https://cdn.cellphones.com.vn/media/ltsoft/promotion/slider-macbook-1200x400.jpg" class="d-block w-100" alt="Banner 2">
            </div>
            <div class="carousel-item">
                <img src="https://cdn.cellphones.com.vn/media/ltsoft/promotion/xiaomi-13t-1200x400.jpg" class="d-block w-100" alt="Banner 3">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
</section>

<!-- ⚡ FLASH SALE -->
<section class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold text-danger"><i class="fas fa-bolt"></i> FLASH SALE TOÀN SÀN</h2>
        <a href="#" class="text-primary">Xem tất cả ></a>
    </div>
    <div class="row g-3">

        <div class="col-6 col-md-3">
            <div class="card h-100 border-0 shadow-sm text-center">
                <div class="position-relative">
                    <img src="https://cdn.tgdd.vn/Products/Images/42/309821/iphone-15-pro-max-blue-thumbnew-600x600.jpg" class="card-img-top p-3" alt="SP">
                    <span class="badge bg-danger position-absolute top-0 start-0 m-2">-15%</span>
                </div>
                <div class="card-body">
                    <h6 class="fw-semibold">iPhone 15 Pro Max 256GB</h6>
                    <p class="text-danger fw-bold mb-1">28.990.000₫ 
                        <span class="text-muted text-decoration-line-through small">31.990.000₫</span>
                    </p>
                    <p class="small text-muted mb-2">Được bán bởi <strong>TechZoneVN</strong></p>
                    <button class="btn btn-outline-primary btn-sm w-100">Thêm vào giỏ</button>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- 🏷️ DANH MỤC SẢN PHẨM -->
<section class="container my-5 text-center">
    <h2 class="fw-bold mb-4">KHÁM PHÁ DANH MỤC</h2>
    <div class="row g-4">
        @php
        $categories = [
            ['name' => 'Điện thoại', 'icon' => ''],
            ['name' => 'Laptop', 'icon' => ''],
            ['name' => 'Tablet', 'icon' => ''],
            ['name' => 'Phụ kiện', 'icon' => ''],
            ['name' => 'Âm thanh', 'icon' => ''],
            ['name' => 'Đồng hồ', 'icon' => ''],
            ['name' => 'Máy tính bảng', 'icon' => ''],
            ['name' => 'Gaming gear', 'icon' => ''],
        ];
        @endphp

        @foreach ($categories as $cat)
        <div class="col-6 col-md-3">
            <div class="p-4 bg-light rounded shadow-sm category-item">
                <img src="{{ $cat['icon'] }}" height="60" alt="{{ $cat['name'] }}">
                <h6 class="mt-3">{{ $cat['name'] }}</h6>
            </div>
        </div>
        @endforeach
    </div>
</section>

<!-- 💎 SẢN PHẨM NỔI BẬT -->
<section class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold">TOP SẢN PHẨM NỔI BẬT</h2>
        <a href="/products" class="text-primary">Xem thêm ></a>
    </div>
    <div class="row g-4">
        
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 text-center">
                <img src="https://cdn.tgdd.vn/Products/Images/42/309821/iphone-15-pro-max-blue-thumbnew-600x600.jpg" class="card-img-top p-3" alt="SP">
                <div class="card-body">
                    <h6 class="fw-semibold">tên sản phẩm</h6>
                    <p class="text-danger fw-bold">giá</p>
                    <p class="small text-muted mb-2">Được bán bởi <strong>Người nào đó</strong></p>
                    <a href="#" class="btn btn-outline-primary btn-sm w-100">Xem chi tiết</a>
                </div>
            </div>
        </div>
      
    </div>
</section>

<!-- 🏬 TOP CỬA HÀNG UY TÍN -->
<section class="container my-5">
    <h2 class="fw-bold text-center mb-4">TOP CỬA HÀNG UY TÍN</h2>
    <div class="row g-4">
        
        <div class="col-6 col-md-3 text-center">
            <div class="p-4 bg-light rounded shadow-sm">
                <img src="https://cdn-icons-png.flaticon.com/512/891/891462.png" height="50" alt="shop">
                <h6 class="mt-3">tenshop</h6>
                <p class="text-muted small">đánh giá</p>
                <a href="#" class="btn btn-sm btn-outline-primary">Xem cửa hàng</a>
            </div>
        </div>
        
    </div>
</section>

<!-- 🎉 ƯU ĐÃI & QUẢNG CÁO -->
<section class="container my-5">
    <div class="row g-3">
        <div class="col-md-6">
            <img src="https://cdn.cellphones.com.vn/media/ltsoft/promotion/uu-dai-thang-10-1.jpg" class="w-100 rounded shadow" alt="promo">
        </div>
        <div class="col-md-6">
            <img src="https://cdn.cellphones.com.vn/media/ltsoft/promotion/uu-dai-thang-10-2.jpg" class="w-100 rounded shadow" alt="promo">
        </div>
    </div>
</section>

@endsection
