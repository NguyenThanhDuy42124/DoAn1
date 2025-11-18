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

        <section class="container my-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="fw-bold">TOP SẢN PHẨM NỔI BẬT</h2>
                {{-- Giả sử bạn có route tên 'products.list' cho trang listproducts --}}
                <a href="{{ route('products.list') }}" class="text-primary">Xem thêm ></a>
            </div>

            <div class="row g-4">
                {{-- Dùng $products->take(8) để chỉ lấy 8 sản phẩm đầu tiên --}}
                @forelse ($products->take(8) as $product)
                    <div class="col-6 col-md-3">
                        {{-- Thêm d-flex flex-column để footer luôn ở dưới cùng --}}
                        <div class="card product-card h-100 shadow-sm d-flex flex-column">

                            {{-- === BỌC LINK BẮT ĐẦU === --}}
                            <a href="{{ route('products.detail', ['id' => $product->id]) }}"
                                class="text-decoration-none text-dark" style="flex-grow: 1;">

                                {{-- Badge giảm giá (Logic từ listproducts) --}}
                                @if ($product->original_price && $product->original_price > $product->price)
                                    <span
                                        class="badge-discount">-{{ round(100 - ($product->price / $product->original_price) * 100) }}%</span>
                                @endif

                                <img src="{{ $product->images->isNotEmpty()
                                    ? asset('storage/' . $product->images->first()->image_path)
                                    : asset('storage/product_images/default.jpg') }}"
                                    alt="{{ $product->name }}" class="card-img-top product-img"
                                    style="height: 200px; object-fit: cover;"> {{-- Đồng bộ style ảnh --}}

                                <div class="card-body"> {{-- Bỏ d-flex và mt-auto --}}
                                    <div>
                                        <h5 class="card-title h6">{{ $product->name }}</h5>
                                        <p class="card-text text-muted small">{{ Str::limit($product->description, 50) }}
                                        </p>

                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <span class="text-muted small">Thương hiệu:</span>
                                                <span class="fw-bold small">{{ $product->brand->name ?? 'N/A' }}</span>
                                            </div>
                                            <div>
                                                <span class="text-muted small">Tồn kho:</span>
                                                <span class="fw-bold small">{{ $product->stock }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Giá được đưa vào đây để có thể click --}}
                                    <div class="mt-3">
                                        <span
                                            class="product-price fs-6 fw-bold text-danger">{{ number_format($product->price, 0, ',', '.') }}₫</span>
                                    </div>
                                </div>
                                {{-- === BỌC LINK KẾT THÚC === --}}
                            </a>

                            {{-- PHẦN NÚT BẤM (ĐỂ BÊN NGOÀI <a>) --}}
                            <div class="card-footer bg-white border-top-0 pt-0 pb-3">
                                <div class="d-flex">
                                    {{-- Nút "Xem" (thay modal bằng link) --}}
                                    <a href="{{ route('products.detail', ['id' => $product->id]) }}"
                                        class="btn btn-outline-primary btn-sm me-2" title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    {{-- Toàn bộ logic nút bấm "Thêm vào giỏ" --}}
                                    <div class="flex-grow-1">
                                        @if (Auth::check())
                                            @if (empty(Auth::user()->phoneNumber) || empty(Auth::user()->email) || empty(Auth::user()->address))
                                                <a href="{{ route('general.users.edit', Auth::user()->id) }}"
                                                    class="btn btn-warning btn-sm w-100" title="Cập nhật thông tin">
                                                    <i class="fas fa-user-edit"></i>
                                                </a>
                                            @else
                                                @if ($product->stock > 0)
                                                    <form action="{{ route('buyer.carts.store') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="product_id"
                                                            value="{{ $product->id }}">
                                                        <input type="hidden" name="quantity" value="1">
                                                        <button type="submit" class="btn btn-success btn-sm w-100"
                                                            title="Thêm vào giỏ">
                                                            <i class="fas fa-cart-plus"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <button class="btn btn-secondary btn-sm w-100" disabled>Hết
                                                        hàng</button>
                                                @endif
                                            @endif
                                        @else
                                            <a href="{{ route('login') }}" class="btn btn-success btn-sm w-100"
                                                title="Thêm vào giỏ">
                                                <i class="fas fa-cart-plus"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-muted">Chưa có sản phẩm nào.</p>
                @endforelse
            </div>
        </section>



        <section class="container my-5">
            <h2 class="fw-bold text-center mb-4">TOP CỬA HÀNG UY TÍN</h2>
            <div class="row g-4">
                @forelse ($shops as $shop)
                    <div class="col-6 col-md-3 text-center">
                        <div class="p-4 bg-light rounded shadow-sm">
                            <img src="{{ !empty($shop->img) ? asset('storage/' . $shop->img) : asset('storage/profile_images/default.jpg') }}"
                                alt="{{ $shop->name }}" class="rounded-circle"
                                style="width: 80px; height: 80px; object-fit: cover;">

                            <h6 class="mt-3">{{ $shop->name }}</h6>
                            <p
                                class="small mb-2 {{ $shop->seller_reviews_avg_rating ? 'text-warning fw-bold' : 'text-muted' }}">
                                <i class="fas fa-star"></i>
                                {{ $shop->seller_reviews_avg_rating ? round($shop->seller_reviews_avg_rating, 1) : 'Chưa có' }}
                            </p>
                            <a href="{{ route('shop.show', ['id' => $shop->id]) }}"
                                class="btn btn-sm btn-outline-primary">Xem cửa hàng</a> </div>
                    </div>
                @empty
                    <p class="text-center text-muted">Chưa có cửa hàng nào.</p>
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