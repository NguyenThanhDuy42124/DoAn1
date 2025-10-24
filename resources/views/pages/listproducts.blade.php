@extends('layouts.app')
@section('title', 'Danh sách sản phẩm')

@section('content')
{{-- CHỈ SỬ DỤNG MỘT CONTAINER DUY NHẤT BỌC BÊN NGOÀI --}}
<div class="container my-4">

    <div class="filter-section card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ url()->current() }}" method="GET">
                <div class="row g-3 align-items-end">

                    <div class="col-md-2">
                        <h6><i class="fas fa-dollar-sign me-2"></i>Khoảng giá</h6>
                        <select class="form-select" name="price_range">
                            <option value="">Tất cả</option>
                            <option value="0-5000000" {{ request('price_range') == '0-5000000' ? 'selected' : '' }}>
                                Dưới 5 triệu
                            </option>
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

                    <div class="col-md-2">
                        <h6><i class="fas fa-tag me-2"></i>Thương hiệu</h6>
                        <select class="form-select" name="brand">
                            <option value="">Tất cả</option>
                            @isset($brands)
                                @foreach($brands as $brand)
                                    <option value="{{ $brand }}" {{ request('brand') == $brand ? 'selected' : '' }}>
                                        {{ $brand }}
                                    </option>
                                @endforeach
                            @endisset
                        </select>
                    </div>

                    <div class="col-md-2">
                        <h6><i class="fas fa-list-ul me-2"></i>Danh mục</h6>
                        <select class="form-select" name="category">
                            <option value="">Tất cả</doption>
                            @isset($categories)
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            @endisset
                        </select>
                    </div>

                    <div class="col-md-2">
                        <h6><i class="fas fa-sliders-h me-2"></i>Tùy chọn</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="discount" id="discount" value="1"
                                   {{ request('discount') ? 'checked' : '' }}>
                            <label class="form-check-label" for="discount">Đang giảm giá</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="in_stock" id="stock" value="1"
                                   {{ request('in_stock') ? 'checked' : '' }}>
                            <label class="form-check-label" for="stock">Còn hàng</label>
                        </div>
                    </div>
                    
                    <div class="col-md-4 d-flex">
                        <button type="submit" class="btn btn-primary me-2 w-100">
                            <i class="fas fa-filter me-1"></i> Lọc
                        </button>
                        <a href="{{ url()->current() }}" class="btn btn-outline-secondary w-100">Xóa lọc</a>
                    </div>
                    
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        {{-- DÙNG @forelse ĐỂ XỬ LÝ TRƯỜNG HỢP RỖNG --}}
        @forelse($products as $product)
            {{-- BỎ @if($product->status === "Approved") VÌ CONTROLLER ĐÃ LỌC --}}
            <div class="col-md-4">
                <div class="card product-card h-100 shadow-sm">
                    <span class="badge-discount">- X%</span> {{-- Bạn có thể ẩn đi nếu chưa có logic giảm giá --}}
                    
                    <img src="{{ $product->images->isNotEmpty() ?
                        asset('storage/' . $product->images->first()->image_path) :
                        asset('storage/product_images/default.jpg') }}"
                         alt="{{ $product->name }}"
                         class="card-img-top product-img">

                    <div class="card-body d-flex flex-column">
                        
                        <div>
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="text-muted small mb-1"> Danh mục: 
                                <span class="fw-semibold text-dark">{{ $product->category->name ?? 'Chưa phân loại' }}</span>
                            </p>
                            <p class="card-text text-muted">{{ Str::limit($product->description, 100) }}</p>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <span class="text-muted small">Thương hiệu:</span>
                                    <span class="fw-bold">{{ $product->brand ?? 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="text-muted small">Tồn kho:</span>
                                    <span class="fw-bold">{{ $product->stock }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-auto">
                            <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-md-center">
                                <div class="mb-2 mb-md-0">
                                    <span class="product-price">{{ number_format($product->price, 0, ',', '.') }}₫</span>
                                </div>
                                <div class="d-flex w-100 w-md-auto">
                                    <button class="btn btn-outline-primary btn-sm me-2" data-bs-toggle="modal"
                                            data-bs-target="#detailModal-{{ $product->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    @if (Auth::check())
                                        @if (empty(Auth::user()->phoneNumber) || empty(Auth::user()->email) || empty(Auth::user()->address))
                                            <a href="{{ route('general.users.edit', Auth::user()->id) }}" class="btn btn-warning btn-sm flex-grow-1">
                                                <i class="fas fa-user-edit"></i> Cập nhật thông tin
                                            </a>
                                        @else
                                            @if($product->stock > 0)
                                                <form action="{{ route('buyer.carts.store') }}" method="POST" class="flex-grow-1">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                    <input type="hidden" name="quantity" value="1">
                                                    <button type="submit" class="btn btn-success btn-sm w-100">
                                                        <i class="fas fa-cart-plus"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <button class="btn btn-secondary btn-sm flex-grow-1" disabled>Hết hàng</button>
                                            @endif
                                        @endif
                                    @else
                                        <a href="{{ route('login') }}" class="btn btn-success btn-sm flex-grow-1">
                                            <i class="fas fa-cart-plus"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        @empty
            {{-- Thêm @empty để thông báo khi không có sản phẩm --}}
            <div class="col-12">
                <div class="alert alert-info text-center">
                    Không tìm thấy sản phẩm nào phù hợp với bộ lọc.
                </div>
            </div>
        @endforelse
    </div> 
    
</div> {{-- (Tùy chọn) Bạn có thể giữ các modal chi tiết sản phẩm ở đây --}}
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