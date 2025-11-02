@extends('layouts.app')

{{-- Tiêu đề trang được hard-code --}}
@section('title', 'PC TTG DESIGNER - 3D RENDER - EDIT VIDEO ULTRA 7 265KF - RTX 4060 8GB')

@section('content')
<div class="container my-5">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4 p-md-5">
            <div class="row g-5">

                {{-- CỘT BÊN TRÁI (HÌNH ẢNH) --}}
                <div class="col-lg-6">
                    @if($product && $product->images->count() > 0)
                    <div id="productCarousel" class="carousel slide mb-3" data-bs-ride="carousel">
                        <div class="carousel-inner rounded border">
                            @foreach($product->images as $index => $image)
                            <div class="carousel-item img- {{ $loop->first ? 'active' : '' }}">
                                <img src="{{ asset('storage/' . $image->image_path) }}" class="d-block w-100 rounded border" alt="Ảnh sản phẩm {{ $index + 1 }}">
                            </div>
                            @endforeach
                        </div>
                        @if (count($product->images) > 1)
                        <button class="carousel-control-prev carousel-dark" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" style="width: 20px; height: 20px;"></span>
                        </button>
                        <button class="carousel-control-next carousel-dark" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" style="width: 20px; height: 20px;"></span>
                        </button>
                        @endif
                    </div>
                    @else
                    <img src="{{ asset('storage/product_images/default.jpg') }}" alt="Ảnh sản phẩm" class="img-fluid rounded border w-100 mb-3">
                    @endif
                    {{-- ẢNH THUMBNAILS BÊN DƯỚI --}}
                    <div class="row g-2 justify-content-center">
                        @foreach($product->images as $index => $image)
                        <div class="col-3 col-md-2">
                            <img src="{{ asset('storage/' . $image->image_path) }}" class="img-thumbnail border border-2 border-transparent w-100" style="cursor:pointer;" data-bs-target="#productCarousel" data-bs-slide-to="{{ $index }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}" aria-label="Slide {{ $index + 1 }}">
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- CỘT BÊN PHẢI (THÔNG TIN) --}}
                <div class="col-lg-6">
                    <h1 class="h3 fw-bold mb-3">{{ $product->name }}</h1>

                    {{-- Giá --}}
                    <div class="mb-3">
                        <span class="text-muted text-decoration-line-through me-2">
                            giá gốc
                        </span>
                        <span class="h2 text-danger fw-bolder">
                            {{$product->price}}₫
                        </span>
                        <span class="badge bg-danger ms-2">discount</span>
                    </div>

                    {{-- Nút bấm --}}
                    <div class="d-grid gap-2 d-sm-flex mb-4">
                        <button class="btn btn-primary btn-lg flex-grow-1">
                            <i class="fas fa-cart-plus me-2"></i> Mua luôn
                        </button>
                        <button class="btn btn-outline-primary btn-lg flex-grow-1">
                            <i class="fas fa-tools me-2"></i> Tư vấn
                        </button>
                    </div>

                    {{-- Chính sách --}}
                    <div class="card bg-light border-0">
                        <div class="card-body">
                            <h5 class="card-title fw-semibold">Chính sách bán hàng</h5>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Cam kết 100% chính hãng</li>
                                <li class="mb-2"><i class="fas fa-shipping-fast text-primary me-2"></i> Giao hàng tận nơi</li>
                                <li><i class="fas fa-headset text-info me-2"></i> Hỗ trợ 24/7</li>
                            </ul>
                        </div>
                    </div>

                    {{-- Mô tả ngắn --}}
                    <div class="mt-4">
                        <h5 class="fw-semibold">Mô tả nhanh</h5>
                        <p class="text-muted">nắng tắt phía sau màn mưa cố nén cơn đau phía sau nụ cười</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- PHẦN THÔNG SỐ KỸ THUẬT & SẢN PHẨM TƯƠNG TỰ --}}
    <div class="row g-5 mt-4">

        {{-- CỘT THÔNG SỐ (Bám sát ảnh) --}}
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-0 fw-bold">Thông số kỹ thuật</h4>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">STT</th>
                                <th scope="col">Mô tả thiết bị</th>
                                <th scope="col">SL</th>
                                <th scope="col">BH</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>CPU Intel Core Ultra 7 265KF</td>
                                <td>1</td>
                                <td>36th</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Mainboard Gigabyte B860M AORUS</td>
                                <td>1</td>
                                <td>36th</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>RAM GEIL SPEAR V 32GB BUSS 5200MHz</td>
                                <td>1</td>
                                <td>36th</td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>Ổ cứng SSD SSTC Oceanic Whitetip 512GB</td>
                                <td>1</td>
                                <td>36th</td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td>Nguồn máy tính AKIO G8750 - 750W</td>
                                <td>1</td>
                                <td>36th</td>
                            </tr>
                            <tr>
                                <td>6</td>
                                <td>CARD MÀN HÌNH COLORFUL GEFORCE RTX 4060</td>
                                <td>1</td>
                                <td>36th</td>
                            </tr>
                            <tr>
                                <td>7</td>
                                <td>Tản nhiệt khí Thermalright Peerless Assassin</td>
                                <td>1</td>
                                <td>36th</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- CỘT SẢN PHẨM TƯƠNG TỰ --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-0 fw-bold">Sản phẩm tương tự</h4>
                </div>
                <div class="card-body">

                    <div class="d-flex align-items-center mb-3">
                        <img src="/storage/product_images/default.jpg" alt="PC AMD GAMING Ryzen 7 5700X" class="border rounded me-3" style="width: 80px; height: 80px; object-fit: cover;">
                        <div>
                            <a href="/products/101" class="text-decoration-none text-dark fw-semibold d-block">
                                PC AMD GAMING Ryzen 7 5700X
                            </a>
                            <strong class="text-danger">12,390,000₫</strong>
                        </div>
                    </div>

                    <div class="d-flex align-items-center mb-3">
                        <img src="/storage/product_images/default.jpg" alt="PC TTG HOME OFFICE Core i3 12100" class="border rounded me-3" style="width: 80px; height: 80px; object-fit: cover;">
                        <div>
                            <a href="/products/102" class="text-decoration-none text-dark fw-semibold d-block">
                                PC TTG HOME OFFICE Core i3 12100
                            </a>
                            <strong class="text-danger">6,880,000₫</strong>
                        </div>
                    </div>

                    <div class="d-flex align-items-center mb-3">
                        <img src="/storage/product_images/default.jpg" alt="PC TTG AMD GAMING PRO Ryzen 5" class="border rounded me-3" style="width: 80px; height: 80px; object-fit: cover;">
                        <div>
                            <a href="/products/103" class="text-decoration-none text-dark fw-semibold d-block">
                                PC TTG AMD GAMING PRO Ryzen 5
                            </a>
                            <strong class="text-danger">22,990,000₫</strong>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection
