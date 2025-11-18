@extends('layouts.app')
@section('title', 'Danh sách sản phẩm')

@section('content')
<div class="container mb-5">

    <div class="filter-section card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('products.list') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <label for="search" class="form-label fw-semibold">
                                <i class="bi bi-search me-1"></i> Tìm theo tên sản phẩm
                            </label>
                            <input type="text" class="form-control" id="search" name="search" placeholder="Nhập tên sản phẩm cần tìm..." value="{{ request('search') }}">
                        </div>
                    </div>
                    {{-- LỌC KHOẢNG GIÁ --}}
                    <div class="col-md-3">
                        <label for="price_range" class="form-label fw-semibold">
                            <i class="fas fa-dollar-sign me-1"></i> Khoảng giá
                        </label>
                        <select class="form-select" name="price_range" id="price_range">
                            <option value="">Tất cả</option>
                            <option value="0-5000000" {{ request('price_range') == '0-5000000' ? 'selected' : '' }}>
                                Dưới 5 triệu
                            </option>
                            {{-- (Các tùy chọn giá khác) --}}
                            <option value="5000000-10000000" {{ request('price_range') == '5000000-10000000' ? 'selected' : '' }}>
                                5 - 10 triệu
                            </option>
                            <option value="10000000-20000000" {{ request('price_range') == '10000000-20000000' ? 'selected' : '' }}>
                                10 - 20 triệu
                            </option>
                            <option value="20000000-" {{ request('price_range') == '20000000-' ? 'selected' : '' }}>
                                Trên 20 triệu
                            </option>
                        </select>
                    </div>

                    {{-- LỌC THƯƠNG HIỆU (Dùng ID) --}}
                    <div class="col-md-3">
                        <label for="brand" class="form-label fw-semibold">
                            <i class="bi bi-apple me-1"></i> Thương hiệu
                        </label>
                        <select class="form-select" name="brand" id="brand">
                            <option value="">Tất cả</option>
                            @foreach($brands as $brand)
                            {{-- Gửi đi $brand->id --}}
                            <option value="{{ $brand->id }}" {{ request('brand') == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- LỌC DANH MỤC (Dùng ID) --}}
                    <div class="col-md-3">
                        <label for="category" class="form-label fw-semibold">
                            <i class="bi bi-tag me-1"></i> Danh mục
                        </label>
                        <select class="form-select" name="category" id="category">
                            <option value="">Tất cả</option>
                            @foreach($categories as $category)
                            {{-- Gửi đi $category->id --}}
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- LỌC CÒN HÀNG & NÚT BẤM --}}
                    <div class="col-md-3">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="in_stock" id="in_stock" {{ request('in_stock') ? 'checked' : '' }}>
                            <label class="form-check-label" for="in_stock">
                                Chỉ hiển thị hàng còn
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-filter"></i> Lọc
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div> {{-- đóng filter-section --}}

    @if(session('success'))
        <div class="mt-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    <div class="row g-4">
        @forelse($products as $product)
        <div class="col-md-3">
            {{-- Thêm d-flex flex-column để footer luôn ở dưới cùng --}}
            <div class="card product-card h-100 shadow-sm d-flex flex-column">

                {{-- === BỌC LINK BẮT ĐẦU === --}}
                {{-- Link này bọc hình ảnh, thân card (thông tin) và giá --}}
                <a href="{{ route('products.detail', ['id' => $product->id]) }}" class="text-decoration-none text-dark" style="flex-grow: 1;"> {{-- flex-grow để link chiếm hết không gian --}}

                    {{-- Badge giảm giá (Tôi đã thêm logic tính % cho bạn) --}}
                    @if($product->original_price && $product->original_price > $product->price)
                    <span class="badge-discount">-{{ round(100 - ($product->price / $product->original_price * 100)) }}%</span>
                    @endif

                    <img src="{{ $product->images->isNotEmpty() ?
                                asset('storage/' . $product->images->first()->image_path) :
                                asset('storage/product_images/default.jpg') }}" alt="{{ $product->name }}" class="card-img-top product-img">

                    {{-- Thân card - Bỏ d-flex và mt-auto --}}
                    <div class="card-body">
                        <div>
                            {{-- Tên sản phẩm (không cần thẻ <a> riêng) --}}
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="text-muted small mb-1"> Danh mục:
                                <span class="fw-semibold text-dark">{{ $product->category->name ?? 'Chưa phân loại' }}</span>
                            </p>
                            <p class="card-text text-muted">{{ Str::limit($product->description, 100) }}</p>

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <span class="text-muted small">Thương hiệu:</span>
                                    <span class="fw-bold">{{ $product->brand->name ?? 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="text-muted small">Tồn kho:</span>
                                    <span class="fw-bold">{{ $product->stock }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Giá được đưa vào đây để có thể click --}}
                        <div class="mt-3"> {{-- Thay mt-auto bằng mt-3 --}}
                            <span class="product-price">{{ number_format($product->price, 0, ',', '.') }}₫</span>
                        </div>
                    </div>
                    {{-- === BỌC LINK KẾT THÚC === --}}
                </a>



                {{-- PHẦN NÚT BẤM (ĐỂ BÊN NGOÀI <a>) --}}
                {{-- Đặt vào card-footer để đảm bảo nó ở dưới cùng --}}
                <div class="card-footer bg-white border-top-0 pt-0 pb-3">
                    <div class="d-flex">

                        {{-- Nút "Xem" (thay modal bằng link) --}}
                        <a href="{{ route('products.detail', ['id' => $product->id]) }}" class="btn btn-outline-primary btn-sm me-2 w-100">
                            <i class="fas fa-eye"></i>
                        </a>

                        {{-- Toàn bộ logic nút bấm "Thêm vào giỏ" --}}
                        <div class="flex-grow-1">
                            @if (Auth::check())
                            @if (empty(Auth::user()->phoneNumber) || empty(Auth::user()->email) || empty(Auth::user()->address))
                            <a href="{{ route('general.users.edit', Auth::user()->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-user-edit"></i> Cập nhật
                            </a>
                            @else
                            @if($product->stock > 0)
                            <form action="{{ route('buyer.carts.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-success btn-sm ">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                            </form>
                            @else
                            <button class="btn btn-secondary btn-sm" disabled>Hết hàng</button>
                            @endif
                            @endif
                            @else
                            <a href="{{ route('login') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-cart-plus"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info text-center">
                Không tìm thấy sản phẩm nào phù hợp với bộ lọc.
            </div>
        </div>
        @endforelse
    </div>
    <div class="mt-4 d-md-flex justify-content-between align-items-center">

        {{-- 1. Phần hiển thị kết quả (bên trái) --}}
        <div class="text-center text-md-start mb-2 mb-md-0">
            @if ($products->total() > 0)
            <p class="text-muted small mb-0">
                Hiển thị <strong>{{ $products->firstItem() }}</strong>
                - <strong>{{ $products->lastItem() }}</strong>
                trong tổng số <strong>{{ $products->total() }}</strong> kết quả
            </p>
            @else
            <p class="text-muted small mb-0">
                Không tìm thấy kết quả nào.
            </p>
            @endif
        </div>

        {{-- 2. Phần nút chuyển trang (bên phải) --}}
        <div class="d-flex justify-content-center justify-content-md-end">
            {{--
          Thêm withQueryString() là RẤT QUAN TRỌNG
          để giữ lại bộ lọc (giá, thương hiệu...) khi chuyển trang.
        --}}
            {{ $products->withQueryString()->links('pagination::bootstrap-5') }}
        </div>

    </div>
</div>

@foreach($products as $product)
<div class="modal fade" id="detailModal-{{ $product->id }}" tabindex="-1" aria-labelledby="detailModalLabel-{{ $product->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel-{{ $product->id }}">{{ $product->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>{{ $product->description }}</p>
                {{-- Thêm chi tiết sản phẩm khác nếu muốn --}}
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection
