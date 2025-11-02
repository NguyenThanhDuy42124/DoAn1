@extends('layouts.app')

{{-- Tiêu đề trang được hard-code --}}
@section('title', 'PC TTG DESIGNER - 3D RENDER - EDIT VIDEO ULTRA 7 265KF - RTX 4060 8GB')

@section('content')
{{-- ĐÃ SỬA: Giảm margin-top --}}
<div class="container mb-5">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4 p-md-5">
            <div class="row g-5">

                {{-- CỘT BÊN TRÁI (HÌNH ẢNH) - ĐÃ SỬA CAROUSEL & THUMBNAILS --}}
                <div class="col-lg-6">
                    @if($product && $product->images->count() > 0)
                    <div id="productCarousel" class="carousel slide mb-3" data-bs-ride="carousel">
                        
                        {{-- Khung ratio 1x1 để giữ kích thước cố định --}}
                        <div class="carousel-inner rounded border" style="background-color: #f8f9fa;">
                            @foreach($product->images as $index => $image)
                            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                <div class="ratio ratio-1x1">
                                    <img src="{{ asset('storage/' . $image->image_path) }}" 
                                         class="d-block w-100 h-100 rounded" 
                                         style="object-fit: contain; object-position: center;" 
                                         alt="Ảnh sản phẩm {{ $index + 1 }}">
                                </div>
                            </div>
                            @endforeach
                        </div>

                        @if (count($product->images) > 1)
                        <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                        @endif
                    </div>

                    {{-- ẢNH THUMBNAILS BÊN DƯỚI (cuộn ngang) --}}
                    <div class="row g-2 flex-nowrap overflow-auto pb-2" style="-webkit-overflow-scrolling: touch;">
                        {{-- Các Thumbnail ảnh sản phẩm --}}
                        @foreach($product->images as $index => $image)
                        <div class="col-3 col-md-2 flex-shrink-0">
                            <img src="{{ asset('storage/' . $image->image_path) }}" 
                                 class="img-thumbnail border border-2 {{ $loop->first ? 'border-primary' : 'border-transparent' }} w-100" 
                                 style="cursor:pointer; height: 80px; object-fit: cover;" 
                                 data-bs-target="#productCarousel" 
                                 data-bs-slide-to="{{ $index }}" 
                                 aria-current="{{ $index === 0 ? 'true' : 'false' }}" 
                                 aria-label="Slide {{ $index + 1 }}" 
                                 onclick="this.closest('.row').querySelectorAll('.img-thumbnail').forEach(img => img.classList.remove('border-primary')); this.classList.add('border-primary');">
                        </div>
                        @endforeach
                    </div>

                    @else
                    {{-- Ảnh mặc định cũng trong khung ratio --}}
                    <div class="ratio ratio-1x1 rounded border" style="background-color: #f8f9fa;">
                        <img src="{{ asset('storage/product_images/default.jpg') }}" 
                             alt="Ảnh sản phẩm" 
                             class="img-fluid rounded w-100 h-100"
                             style="object-fit: contain; object-position: center;">
                    </div>
                    @endif
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

                    {{-- ĐÃ SỬA: "Mô tả sản phẩm" thay thế "Mô tả nhanh" --}}
                    <div class="card bg-light border-0 mt-4">
                        <h5 class="fw-semibold">Mô tả sản phẩm</h5>
                        {{-- Giả sử $product->description là văn bản thuần túy --}}
                        {{-- Nếu nó là HTML, bạn nên dùng {!! $product->description !!} --}}
                        <p class="text-muted" style="white-space: pre-wrap;">{{ $product->description }}</p>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- ĐÃ XÓA: Card "Mô tả sản phẩm" riêng biệt ở đây --}}

    {{-- Card "Người bán" --}}
    <div class="row g-5 mt-4">
        <div class="col">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-0 fw-bold">Người bán</h4>
                </div>
                <div class="card-body d-flex align-items-center">
                    <img src="{{ asset('storage/' . $seller->img)  }}" alt="{{asset('storage/product_images/default.jpg')}}" class="rounded-circle border me-4" style="width: 80px; height: 80px; object-fit: cover;">
                    <div>
                        <h5 class="fw-semibold mb-1">{{ $seller->name }}</h5>
                        <p class="mb-2 text-muted">Địa chỉ: {{ $seller->address }}</p>
                        <a href="/shop/{{ $seller->id }}" class="btn btn-outline-primary btn-sm">
                            Xem cửa hàng
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- PHẦN THÔNG SỐ KỸ THUẬT & SẢN PHẨM TƯƠNG TỰ --}}
    <div class="row g-5 mt-4">

        {{-- CỘT THÔNG SỐ --}}
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
                                GỌN LẠI (TƯƠNG TỰ) ...
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