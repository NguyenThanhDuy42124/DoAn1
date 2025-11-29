@extends('layouts.app')

@section('content')
    <div class="container">
        @livewire('main-page')
        <!--
                                        <section class="container my-5">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h2 class="fw-bold text-danger"><i class="fas fa-bolt"></i> FLASH SALE TOÀN SÀN</h2>
                                                <a href="/products" class="text-primary">Xem tất cả ></a>
                                            </div>
                                            <div class="row g-3">

                                                <div class="col-6 col-md-3">
                                                    <div class="card h-100 border-0 shadow-sm text-center">
                                                        <div class="position-relative">
                                                            <img src="https://cdn.tgdd.vn/Products/Images/42/309821/iphone-15-pro-max-blue-thumbnew-600x600.jpg"
                                                                class="card-img-top p-3" alt="SP">
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
                                    -->
        <section class="container my-5 category-section">
            <div class="text-center mb-5">
                <h2 class="fw-bold display-6">KHÁM PHÁ DANH MỤC</h2>
                <div class="d-inline-block bg-primary" style="height: 4px; width: 60px; border-radius: 2px;"></div>
            </div>

            <div class="row g-4">
                @php
                    // Lưu ý: Tốt nhất nên truyền biến này từ Controller thay vì gọi trực tiếp ở View
                    $categories = app(App\Http\Controllers\CategoryController::class)->getCategories();

                    // Mảng màu gradient ngẫu nhiên để mỗi danh mục 1 màu khác nhau (cho sinh động)
                    $gradients = [
                        'linear-gradient(45deg, #ff9a9e 0%, #fad0c4 99%, #fad0c4 100%)',
                        'linear-gradient(120deg, #a1c4fd 0%, #c2e9fb 100%)',
                        'linear-gradient(120deg, #84fab0 0%, #8fd3f4 100%)',
                        'linear-gradient(120deg, #fccb90 0%, #d57eeb 100%)',
                    ];
                @endphp

                @foreach ($categories as $index => $cat)
                    <div class="col-6 col-md-3">
                        <a href="{{ route('products.list', ['category' => $cat->id]) }}" class="text-decoration-none">
                            <div class="modern-cat-card">
                                {{-- Tạo Icon tự động từ chữ cái đầu và màu ngẫu nhiên --}}
                                <div class="cat-icon-placeholder"
                                    style="background: {{ $gradients[$index % count($gradients)] }};">
                                    {{ Str::substr($cat->name, 0, 1) }}
                                </div>

                                <h6>{{ $cat->name }}</h6>

                                {{-- Mũi tên chỉ hiện khi hover --}}
                                <div class="cat-arrow">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="container my-5">
            {{-- HEADER VÀ BỘ LỌC --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-2 mb-md-0 display-6">SẢN PHẨM NỔI BẬT</h2>
                    <div class="bg-primary d-none d-md-block" style="height: 4px; width: 60px; border-radius: 2px;"></div>
                </div>

                <div class="d-flex align-items-center gap-3 mt-3 mt-md-0">
                    <div class="btn-group shadow-sm" role="group">
                        <a href="{{ route('home', ['sort' => 'newest']) }}"
                            class="btn btn-sm px-3 {{ request('sort') == 'newest' || !request('sort') ? 'btn-primary' : 'btn-outline-primary' }}">Mới
                            nhất</a>
                        <a href="{{ route('home', ['sort' => 'best_seller']) }}"
                            class="btn btn-sm px-3 {{ request('sort') == 'best_seller' ? 'btn-primary' : 'btn-outline-primary' }}">Bán
                            chạy</a>
                        <a href="{{ route('home', ['sort' => 'top_rated']) }}"
                            class="btn btn-sm px-3 {{ request('sort') == 'top_rated' ? 'btn-primary' : 'btn-outline-primary' }}">Đánh
                            giá cao</a>
                    </div>
                    <a href="{{ route('products.list') }}"
                        class="text-primary text-decoration-none fw-bold text-nowrap ms-2">Xem tất cả <i
                            class="fas fa-arrow-right small ms-1"></i></a>
                </div>
            </div>

            {{-- DANH SÁCH SẢN PHẨM --}}
            <div class="row g-4">
                @forelse ($products as $product)
                    <div class="col-6 col-md-3">

                        {{-- CARD BẮT ĐẦU --}}
                        <div class="modern-product-card">

                            {{-- Link bao quanh Ảnh & Nội dung (trừ nút bấm) --}}
                            <a href="{{ route('products.detail', ['id' => $product->id]) }}"
                                class="text-decoration-none text-dark d-contents">

                                {{-- 1. Ảnh sản phẩm --}}
                                <div class="product-img-wrapper">
                                    @if ($product->original_price && $product->original_price > $product->price)
                                        <div class="modern-discount-badge">
                                            -{{ round(100 - ($product->price / $product->original_price) * 100) }}%
                                        </div>
                                    @endif

                                    <img src="{{ $product->images->isNotEmpty() ? asset('storage/' . $product->images->first()->image_path) : asset('storage/product_images/default.jpg') }}"
                                        alt="{{ $product->name }}" class="modern-product-img">
                                </div>

                                {{-- 2. Nội dung (Body) --}}
                                <div class="product-body">
                                    {{-- Tên --}}
                                    <h5 class="product-title" title="{{ $product->name }}">
                                        {{ $product->name }}
                                    </h5>

                                    {{-- Thông tin phụ --}}
                                    <div class="d-flex justify-content-between align-items-center mb-2 small text-muted">
                                        <span>{{ Str::limit($product->brand->name ?? 'N/A', 15) }}</span>

                                        {{-- Logic hiển thị phụ (Rating hoặc Tồn kho) --}}
                                        @if (request('sort') == 'top_rated' && isset($product->reviews_avg_rating))
                                            <span class="text-warning fw-bold"><i class="fas fa-star"></i>
                                                {{ round($product->reviews_avg_rating, 1) }}</span>
                                        @elseif (request('sort') == 'best_seller' && isset($product->order_items_sum_quantity))
                                            <span class="text-success fw-bold">Đã bán:
                                                {{ $product->order_items_sum_quantity }}</span>
                                        @else
                                            <span>Kho: <b>{{ $product->stock }}</b></span>
                                        @endif
                                    </div>

                                    {{-- Giá tiền --}}
                                    <div class="mt-auto">
                                        <span class="product-price-highlight">
                                            {{ number_format($product->price, 0, ',', '.') }}₫
                                        </span>
                                        @if ($product->original_price > $product->price)
                                            <span class="text-muted text-decoration-line-through small ms-1">
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
                                        class="btn btn-outline-primary btn-action-sm" title="Xem">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    {{-- Nút Mua --}}
                                    <div class="flex-grow-1">
                                        @if (Auth::check())
                                            @if (empty(Auth::user()->phoneNumber) || empty(Auth::user()->address))
                                                <a href="{{ route('general.users.edit', Auth::user()->id) }}"
                                                    class="btn btn-warning btn-action-sm w-100 text-white"
                                                    title="Cập nhật thông tin">
                                                    <i class="fas fa-user-edit"></i>
                                                </a>
                                            @else
                                                @if ($product->stock > 0)
                                                    <form action="{{ route('buyer.carts.store') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="product_id"
                                                            value="{{ $product->id }}">
                                                        <input type="hidden" name="quantity" value="1">
                                                        <button type="submit" class="btn btn-success btn-action-sm w-100">
                                                            <i class="fas fa-cart-plus me-1"></i> Thêm
                                                        </button>
                                                    </form>
                                                @else
                                                    <button class="btn btn-secondary btn-action-sm w-100" disabled>Hết
                                                        hàng</button>
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
                        {{-- CARD KẾT THÚC --}}

                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="opacity-50">
                            <i class="fas fa-box-open fa-3x mb-3 text-secondary"></i>
                        </div>
                        <p class="text-muted">Chưa có sản phẩm nào phù hợp.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <section class="container my-5">
            <div class="text-center mb-5">
                <h2 class="fw-bold display-6">TOP CỬA HÀNG UY TÍN</h2>
                <div class="d-inline-block bg-primary" style="height: 4px; width: 60px; border-radius: 2px;"></div>
            </div>

            <div class="row g-4">
                @forelse ($shops as $index => $shop)
                    <div class="col-6 col-md-3">
                        <div class="modern-shop-card pb-4">
                            {{-- Banner màu --}}
                            {{-- Mẹo: Dùng index để thay đổi màu banner cho mỗi shop khác nhau --}}
                            @php
                                $gradients = [
                                    'linear-gradient(120deg, #84fab0 0%, #8fd3f4 100%)', // Xanh ngọc
                                    'linear-gradient(120deg, #fccb90 0%, #d57eeb 100%)', // Cam tím
                                    'linear-gradient(120deg, #e0c3fc 0%, #8ec5fc 100%)', // Tím xanh
                                    'linear-gradient(120deg, #ff9a9e 0%, #fecfef 99%, #fecfef 100%)', // Hồng phấn
                                ];
                                $bgGradient = $gradients[$index % count($gradients)];
                            @endphp
                            <div class="shop-card-banner" style="background: {{ $bgGradient }};"></div>

                            {{-- Avatar Shop --}}
                            <div class="shop-avatar-wrapper">
                                <img src="{{ !empty($shop->img) ? asset('storage/' . $shop->img) : asset('storage/profile_images/default.jpg') }}"
                                    alt="{{ $shop->name }}" class="shop-avatar-img">

                                {{-- Thêm icon tích xanh cho xịn --}}
                                <div class="verified-badge" title="Đã xác minh">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>

                            {{-- Thông tin Shop --}}
                            <div class="px-3">
                                <h6 class="shop-name text-truncate">{{ $shop->name }}</h6>

                                <p class="small mb-3">
                                    @if ($shop->seller_reviews_avg_rating)
                                        <span class="text-warning fw-bold">
                                            <i class="fas fa-star"></i> {{ round($shop->seller_reviews_avg_rating, 1) }}
                                        </span>
                                        <span class="text-muted">/ 5.0</span>
                                    @else
                                        <span class="text-muted">Chưa có đánh giá</span>
                                    @endif
                                </p>

                                <a href="{{ route('shop.show', ['id' => $shop->id]) }}"
                                    class="btn btn-sm btn-outline-primary btn-shop-view w-100">
                                    Xem Shop <i class="fas fa-arrow-right ms-1 small"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <p class="text-muted fs-5">Chưa có cửa hàng nổi bật nào.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <section class="container my-5">
            <div class="row g-3">
                <div class="col-md-6">
                    <img src="https://cdnv2.tgdd.vn/mwg-static/tgdd/Banner/8f/04/8f0489b955b6830ca76cf81d88c637b1.png"
                        class="w-100 rounded shadow" alt="promo">
                </div>
                <div class="col-md-6">
                    <img src="https://cdnv2.tgdd.vn/mwg-static/tgdd/Banner/05/5b/055b9ec5647886f88b0b64f14b1b5971.png"
                        class="w-100 rounded shadow" alt="promo">
                </div>
            </div>
        </section>
    </div>
@endsection
