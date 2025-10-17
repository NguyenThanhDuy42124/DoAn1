@extends('layouts.app')

@section('content')

<!-- 🌈 Banner chính -->
<section class="container-fluid px-0">
    <div id="mainCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="https://cdn2.cellphones.com.vn/insecure/rs:fill:1036:450/q:90/plain/https://dashboard.cellphones.com.vn/storage/AW11-opensale.png" class="d-block w-100" alt="Banner 1">
            </div>
            <div class="carousel-item">
                <img src="https://cdn2.cellphones.com.vn/insecure/rs:fill:1036:450/q:90/plain/https://dashboard.cellphones.com.vn/storage/690x300_Teasing-Sliding_20.png" class="d-block w-100" alt="Banner 2">
            </div>
            <div class="carousel-item">
                <img src="https://cdn2.cellphones.com.vn/insecure/rs:fill:1036:450/q:90/plain/https://dashboard.cellphones.com.vn/storage/690x300_iPhone_17_Pro_Opensale_v3.png" class="d-block w-100" alt="Banner 3">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" style="width: 20px; height: 20px;"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" style="width: 20px; height: 20px;"></span>
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
        $categories = app(App\Http\Controllers\CategoryController::class)->getCategories();
        @endphp

        @foreach ($categories as $cat)
        <div class="col-6 col-md-3">
            <div class="p-4 bg-light rounded shadow-sm category-item">
                <h6 class="mt-3">{{ $cat->name }}</h6>
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
        @forelse ($products as $product)
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100 text-center">
                    <img src="{{ $product->images->isNotEmpty() ?
                    asset('storage/' . $product->images->first()->image_path) :
                    asset('storage/product_images/default.jpg') }}" 
                         class="card-img-top p-3" 
                         alt="{{ $product->name }}">
                    <div class="card-body">
                        <h6 class="fw-semibold">{{ $product->name }}</h6>
                        <p class="text-danger fw-bold">
                            {{ number_format($product->price, 0, ',', '.') }}₫
                        </p>
                        <p class="small text-muted mb-2">
                            Được bán bởi <strong>{{ $product->seller->name ?? 'Không rõ' }}</strong>
                        </p>
                        <a href="/products/{{ $product->id }}" 
                           class="btn btn-outline-primary btn-sm w-100">
                           Xem chi tiết
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-center text-muted">Chưa có sản phẩm nổi bật nào.</p>
        @endforelse
    </div>
</section>


<!-- 🏬 TOP CỬA HÀNG UY TÍN -->
<section class="container my-5">
    <h2 class="fw-bold text-center mb-4">TOP CỬA HÀNG UY TÍN</h2>
    <div class="row g-4">
        @forelse ($shops as $shop)
            <div class="col-6 col-md-3 text-center">
                <div class="p-4 bg-light rounded shadow-sm">
                    <img src="{{ !empty($shop->img)
                        ? asset('storage/' . $shop->img)
                        : asset('storage/profile_images/default.jpg') }}"
                        alt="{{ $shop->name }}"
                        class="rounded-circle"
                        style="width: 80px; height: 80px; object-fit: cover;">

                    <h6 class="mt-3">{{ $shop->name }}</h6>
                    <p class="text-muted small">Đánh giá: {{ $shop->rating ?? 'Chưa có' }}</p>
                    <a href="#" class="btn btn-sm btn-outline-primary">Xem cửa hàng</a>
                </div>
            </div>
        @empty
            <p class="text-center text-muted">Chưa có cửa hàng nào.</p>
        @endforelse
    </div>
</section>

<!-- 🎉 ƯU ĐÃI & QUẢNG CÁO -->
<section class="container my-5">
    <div class="row g-3">
        <div class="col-md-6">
            <img src="https://cdnv2.tgdd.vn/mwg-static/tgdd/Banner/8f/04/8f0489b955b6830ca76cf81d88c637b1.png" class="w-100 rounded shadow" alt="promo">
        </div>
        <div class="col-md-6">
            <img src="https://cdnv2.tgdd.vn/mwg-static/tgdd/Banner/05/5b/055b9ec5647886f88b0b64f14b1b5971.png" class="w-100 rounded shadow" alt="promo">
        </div>
    </div>
</section>

@endsection
