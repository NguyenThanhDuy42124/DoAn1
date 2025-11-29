@extends('layouts.app')

@section('content')
    <div class="container mb-5">

        {{-- PHẦN HEADER SHOP (GIỮ NGUYÊN) --}}
        <section class="shop-header-wrapper mb-4">
            <div class="shop-banner"></div>

            <div class="shop-info bg-white p-4 rounded shadow-sm">
                <div class="d-flex flex-column flex-md-row align-items-center">
                    <img src="{{ !empty($shop->img) ? asset('storage/' . $shop->img) : asset('storage/profile_images/default.jpg') }}"
                        alt="Logo {{ $shop->name }}" class="rounded-circle shop-avatar-wrapper"
                        style="width: 120px; height: 120px; object-fit: cover;">

                    <div class="ms-md-4 text-center text-md-start mt-3 mt-md-0">
                        <h1 class="fw-bold display-6 mb-1">{{ $shop->name }}</h1>
                        <p class="text-muted mb-2">Tham gia từ: {{ $shop->created_at->format('d/m/Y') }}</p>
                        <form action="{{ route('seller.follow.toggle', $shop->id) }}" method="POST"
                            style="display: inline-block;">
                            @csrf

                            @if (Auth::check())
                                {{-- Kiểm tra xem user có phải chính là chủ shop không --}}
                                @if (Auth::id() != $shop->id)
                                    @if ($isFollowing)
                                        <button type="submit" class="btn btn-outline-secondary">
                                            <i class="fas fa-check me-1"></i> Đang theo dõi
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-plus me-1"></i> Theo dõi
                                        </button>
                                    @endif
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="btn btn-danger">
                                    <i class="fas fa-plus me-1"></i> Theo dõi
                                </a>
                            @endif
                        </form>

                    </div>

                    <div class="ms-md-auto mt-4 mt-md-0 d-flex text-center">
                        <div class="px-4 px-lg-4">
                            <div class="fs-4 fw-bold">{{ $totalProductCount }}</div>
                            <div class="text-muted small">Sản phẩm</div>
                        </div>
                        <div class="px-4 px-lg-4">
                            <div class="fs-4 fw-bold">
                                {{-- Hiển thị rating đã tính, làm tròn 1 chữ số. Nếu = 0 thì hiển thị 'Mới' --}}
                                {{ $shopRating ? number_format($shopRating, 1) : 'Mới' }}
                                <i class="fas fa-star text-warning"></i>
                            </div>
                            {{-- Hiển thị tổng số lượt đánh giá --}}
                            <div class="text-muted small">Đánh giá ({{ $shopReviewCount }})</div>
                        </div>
                        <div class="px-4 px-lg-4">
                            <div class="fs-4 fw-bold">{{ $followerCount }}</div>
                            <div class="text-muted small">Theo dõi</div>
                        </div>

                    </div>
                </div>
            </div>
        </section>

        <div class="row g-4 mt-4">

            {{-- SIDEBAR DANH MỤC (GIỮ NGUYÊN) --}}
            <div class="col-lg-3">
                <div class="card shadow-sm border-0 sticky-top" style="top: 20px; border-radius: 16px; overflow: hidden;">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="fw-bold mb-0"><i class="fas fa-list-ul me-2"></i> Danh mục</h5>
                    </div>
                    <div class="list-group list-group-flush p-2">
                        <a href="{{ route('shop.show', array_merge(['id' => $shop->id], request()->except('category', 'page'))) }}"
                            class="list-group-item list-group-item-action {{ !$selectedCategory ? 'active' : '' }}"
                            style="border-radius: 8px;"
                            aria-current="{{ !$selectedCategory ? 'true' : 'false' }}">
                            Tất cả sản phẩm
                        </a>

                        @foreach ($categories as $category)
                            <a href="{{ route('shop.show', array_merge(['id' => $shop->id], request()->query(), ['category' => $category->id, 'page' => 1])) }}"
                                class="list-group-item list-group-item-action {{ (int) $selectedCategory === $category->id ? 'active' : '' }}"
                                style="border-radius: 8px; margin-top: 2px;"
                                aria-current="{{ (int) $selectedCategory === $category->id ? 'true' : 'false' }}">
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-lg-9">

                {{-- TOOLBAR SẮP XẾP (GIỮ NGUYÊN) --}}
                <div class="d-flex align-items-center bg-white p-3 rounded shadow-sm mb-4" style="border-radius: 16px !important;">
                    <span class="fw-bold me-3">Sắp xếp theo:</span>
                    <div class="nav nav-pills" role="tablist">

                        <a class="nav-link btn-sm py-1 px-3 {{ $sort === 'newest' ? 'active' : '' }}"
                            href="{{ route('shop.show', array_merge(['id' => $shop->id], request()->query(), ['sort' => 'newest'])) }}">
                            Mới nhất
                        </a>

                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle btn-sm py-1 px-3 {{ in_array($sort, ['price_asc', 'price_desc']) ? 'active' : '' }}"
                                data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">
                                Giá
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item {{ $sort === 'price_asc' ? 'active' : '' }}"
                                        href="{{ route('shop.show', array_merge(['id' => $shop->id], request()->query(), ['sort' => 'price_asc'])) }}">
                                        Giá: Thấp đến Cao
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ $sort === 'price_desc' ? 'active' : '' }}"
                                        href="{{ route('shop.show', array_merge(['id' => $shop->id], request()->query(), ['sort' => 'price_desc'])) }}">
                                        Giá: Cao đến Thấp
                                    </a>
                                </li>
                            </ul>
                        </div>

                    </div>
                </div>

                {{-- DANH SÁCH SẢN PHẨM (ĐÃ CẬP NHẬT MODERN CARD) --}}
                <section class="shop-products">
                    <div class="row g-4">
                        @forelse ($products as $product)
                            <div class="col-6 col-md-4"> {{-- Grid 3 cột cho trang Shop (vì có sidebar) --}}
                                
                                {{-- BẮT ĐẦU MODERN CARD --}}
                                <div class="modern-product-card">

                                    {{-- Link bao quanh ảnh và nội dung --}}
                                    <a href="{{ route('products.detail', ['id' => $product->id]) }}" 
                                       class="text-decoration-none text-dark d-contents">
                                        
                                        {{-- 1. Ảnh sản phẩm --}}
                                        <div class="product-img-wrapper">
                                            {{-- Badge giảm giá --}}
                                            @if ($product->original_price && $product->original_price > $product->price)
                                                <div class="modern-discount-badge">
                                                    -{{ round(100 - ($product->price / $product->original_price) * 100) }}%
                                                </div>
                                            @endif

                                            <img src="{{ $product->images->isNotEmpty()
                                                    ? asset('storage/' . $product->images->first()->image_path)
                                                    : asset('storage/product_images/default.jpg') }}"
                                                alt="{{ $product->name }}" class="modern-product-img">
                                        </div>

                                        {{-- 2. Nội dung (Body) --}}
                                        <div class="product-body">
                                            {{-- Tên sản phẩm --}}
                                            <h5 class="product-title" title="{{ $product->name }}">
                                                {{ $product->name }}
                                            </h5>
                                            
                                            {{-- Thông tin phụ --}}
                                            <div class="mb-2">
                                                 <span class="badge bg-light text-dark border fw-normal me-1">
                                                    {{ Str::limit($product->category->name ?? 'Khác', 10) }}
                                                </span>
                                                <small class="text-muted">
                                                    Kho: <span class="fw-bold">{{ $product->stock }}</span>
                                                </small>
                                            </div>

                                            {{-- Giá tiền (Luôn nằm đáy body) --}}
                                            <div class="mt-auto">
                                                <span class="product-price-highlight">
                                                    {{ number_format($product->price, 0, ',', '.') }}₫
                                                </span>
                                                @if ($product->original_price > $product->price)
                                                    <span class="text-muted text-decoration-line-through small ms-1" style="font-size: 0.8rem;">
                                                        {{ number_format($product->original_price, 0, ',', '.') }}₫
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </a>

                                    {{-- 3. Nút bấm (Footer) --}}
                                    <div class="product-footer">
                                        <div class="d-flex gap-2">
                                            {{-- Nút Xem --}}
                                            <a href="{{ route('products.detail', ['id' => $product->id]) }}" 
                                               class="btn btn-outline-primary btn-action-sm" 
                                               title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            {{-- Logic Nút Thêm vào giỏ --}}
                                            <div class="flex-grow-1">
                                                @if (Auth::check())
                                                    @if (empty(Auth::user()->phoneNumber) || empty(Auth::user()->email) || empty(Auth::user()->address))
                                                        <a href="{{ route('general.users.edit', Auth::user()->id) }}"
                                                            class="btn btn-warning btn-action-sm w-100 text-white"
                                                            title="Cập nhật thông tin">
                                                            <i class="fas fa-user-edit"></i>
                                                        </a>
                                                    @else
                                                        @if ($product->stock > 0)
                                                            <form action="{{ route('buyer.carts.store') }}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                                <input type="hidden" name="quantity" value="1">
                                                                <button type="submit" class="btn btn-success btn-action-sm w-100">
                                                                    <i class="fas fa-cart-plus me-1"></i> Thêm
                                                                </button>
                                                            </form>
                                                        @else
                                                            <button class="btn btn-secondary btn-action-sm w-100" disabled>Hết hàng</button>
                                                        @endif
                                                    @endif
                                                @else
                                                    <a href="{{ route('login') }}" class="btn btn-success btn-action-sm w-100">
                                                        <i class="fas fa-cart-plus me-1"></i> Thêm
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                </div> 
                                {{-- KẾT THÚC CARD --}}

                            </div>
                        @empty
                            <div class="col-12">
                                <div class="bg-light p-5 rounded text-center" style="border-radius: 16px !important;">
                                    <i class="fas fa-box-open fs-1 text-muted mb-3"></i>
                                    <p class="text-muted fs-5 mt-3">Cửa hàng này chưa có sản phẩm nào.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-5 d-flex justify-content-center">
                        {{ $products->links('pagination::bootstrap-5') }}
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection