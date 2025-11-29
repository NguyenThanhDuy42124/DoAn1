@extends('layouts.app')
@section('title', 'Danh sách sản phẩm')

@section('content')
<div class="container mb-5">

    {{-- PHẦN BỘ LỌC (FILTER) - GIỮ NGUYÊN --}}
    <div class="filter-section card shadow-sm mb-4 border-0" style="border-radius: 16px;">
        <div class="card-body p-4">
            <form action="{{ route('products.list') }}" method="GET">
                <div class="row g-3 align-items-end">
                    {{-- TÌM KIẾM --}}
                    <div class="col-md-12 mb-2">
                        <label for="search" class="form-label fw-bold text-secondary small text-uppercase">
                            <i class="bi bi-search me-1"></i> Tìm kiếm sản phẩm
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0" id="search" name="search" placeholder="Nhập tên sản phẩm..." value="{{ request('search') }}">
                        </div>
                    </div>

                    {{-- LỌC KHOẢNG GIÁ --}}
                    <div class="col-md-3">
                        <label for="price_range" class="form-label fw-bold text-secondary small text-uppercase">
                            <i class="fas fa-dollar-sign me-1"></i> Khoảng giá
                        </label>
                        <select class="form-select" name="price_range" id="price_range">
                            <option value="">Tất cả</option>
                            <option value="0-5000000" {{ request('price_range') == '0-5000000' ? 'selected' : '' }}>Dưới 5 triệu</option>
                            <option value="5000000-10000000" {{ request('price_range') == '5000000-10000000' ? 'selected' : '' }}>5 - 10 triệu</option>
                            <option value="10000000-20000000" {{ request('price_range') == '10000000-20000000' ? 'selected' : '' }}>10 - 20 triệu</option>
                            <option value="20000000-" {{ request('price_range') == '20000000-' ? 'selected' : '' }}>Trên 20 triệu</option>
                        </select>
                    </div>

                    {{-- LỌC THƯƠNG HIỆU --}}
                    <div class="col-md-3">
                        <label for="brand" class="form-label fw-bold text-secondary small text-uppercase">
                            <i class="bi bi-apple me-1"></i> Thương hiệu
                        </label>
                        <select class="form-select" name="brand" id="brand">
                            <option value="">Tất cả</option>
                            @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ request('brand') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- LỌC DANH MỤC --}}
                    <div class="col-md-3">
                        <label for="category" class="form-label fw-bold text-secondary small text-uppercase">
                            <i class="bi bi-tag me-1"></i> Danh mục
                        </label>
                        <select class="form-select" name="category" id="category">
                            <option value="">Tất cả</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- NÚT LỌC --}}
                    <div class="col-md-3">
                        <div class="form-check mb-2 small">
                            <input class="form-check-input" type="checkbox" name="in_stock" id="in_stock" {{ request('in_stock') ? 'checked' : '' }}>
                            <label class="form-check-label text-muted" for="in_stock">
                                Chỉ hiện hàng còn
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold">
                            <i class="bi bi-funnel-fill me-1"></i> Áp dụng bộ lọc
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- THÔNG BÁO THÀNH CÔNG --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- DANH SÁCH SẢN PHẨM (ĐÃ CHỈNH GIAO DIỆN MỚI) --}}
    <div class="row g-4">
        @forelse($products as $product)
        <div class="col-6 col-md-3"> {{-- BẮT ĐẦU CARD HIỆN ĐẠI --}}
            <div class="modern-product-card">

                {{-- Link bao quanh ảnh và nội dung --}}
                <a href="{{ route('products.detail', ['id' => $product->id]) }}" class="text-decoration-none text-dark d-contents">
                    
                    {{-- 1. Ảnh sản phẩm --}}
                    <div class="product-img-wrapper">
                        {{-- Badge giảm giá --}}
                        @if($product->original_price && $product->original_price > $product->price)
                            <div class="modern-discount-badge">
                                -{{ round(100 - ($product->price / $product->original_price * 100)) }}%
                            </div>
                        @endif

                        <img src="{{ $product->images->isNotEmpty() ? 
                                    asset('storage/' . $product->images->first()->image_path) : 
                                    asset('storage/product_images/default.jpg') }}" 
                             alt="{{ $product->name }}" 
                             class="modern-product-img">
                    </div>

                    {{-- 2. Nội dung (Body) --}}
                    <div class="product-body">
                        {{-- Tên sản phẩm --}}
                        <h5 class="product-title" title="{{ $product->name }}">
                            {{ $product->name }}
                        </h5>
                        
                        {{-- Thông tin phụ: Danh mục & Thương hiệu --}}
                        <div class="mb-2">
                            <span class="badge bg-light text-dark border fw-normal me-1">
                                {{ Str::limit($product->category->name ?? 'Khác', 10) }}
                            </span>
                            <small class="text-muted">
                                {{ $product->brand->name ?? '' }}
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
                                @if (empty(Auth::user()->phoneNumber) || empty(Auth::user()->address))
                                    <a href="{{ route('general.users.edit', Auth::user()->id) }}" 
                                       class="btn btn-warning btn-action-sm w-100 text-white" 
                                       title="Cập nhật thông tin">
                                        <i class="fas fa-user-edit"></i>
                                    </a>
                                @else
                                    @if($product->stock > 0)
                                        <form action="{{ route('buyer.carts.add') }}" method="POST">
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
            <div class="text-center py-5">
                <div class="opacity-50 mb-3">
                    <i class="bi bi-inbox fs-1 text-secondary"></i>
                </div>
                <h5 class="text-muted">Không tìm thấy sản phẩm nào phù hợp.</h5>
                <p class="text-secondary small">Hãy thử thay đổi bộ lọc hoặc tìm kiếm từ khóa khác.</p>
                <a href="{{ route('products.list') }}" class="btn btn-outline-secondary btn-sm mt-2">Xóa bộ lọc</a>
            </div>
        </div>
        @endforelse
    </div>

    {{-- PHÂN TRANG --}}
    <div class="mt-5 border-top pt-4 d-md-flex justify-content-between align-items-center">
        {{-- Hiển thị kết quả --}}
        <div class="text-center text-md-start mb-3 mb-md-0 text-muted small">
            @if ($products->total() > 0)
                Hiển thị <strong>{{ $products->firstItem() }}</strong> - <strong>{{ $products->lastItem() }}</strong> 
                trong tổng số <strong>{{ $products->total() }}</strong> sản phẩm
            @endif
        </div>

        {{-- Nút phân trang --}}
        <div class="d-flex justify-content-center justify-content-md-end">
            {{ $products->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection