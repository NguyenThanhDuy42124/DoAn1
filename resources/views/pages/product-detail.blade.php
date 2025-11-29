@extends('layouts.app')

{{-- Tiêu đề trang (Lấy tự động từ tên sản phẩm) --}}
@section('title', $product->name)

@section('content')
<div class="container mb-5">
    

    <div class="card shadow-sm border-0" style="border-radius: 16px;"> {{-- Bo góc thẻ chính --}}
        @auth
            @php
                $restrictedStatuses = ['Pending', 'Rejected', 'Hidden'];
            @endphp

            @if(in_array($product->status, $restrictedStatuses) &&
            (auth()->user()->role === 'admin' || auth()->user()->id === $seller->id))
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center" style="border-radius: 16px 16px 0 0;">
                    <h4 class="mb-0 fw-bold text-danger">
                        Demo sản phẩm sau khi lên sàn
                    </h4>

                    @if(auth()->user()->role === 'admin')
                        @livewire('admin.product-approval', ['productId' => $product->id])
                    @elseif(auth()->user()->id === $seller->id)
                        <a href="{{ route('seller.products.index') }}" class="btn btn-outline-success btn-sm">
                            ← Trở về trang quản lý sản phẩm
                        </a>
                    @endif
                </div>
            @endif
        @endauth

        <div class="card-body p-4 p-md-5">
            <div class="row g-5">

                {{-- ============================================= --}}
                {{-- CỘT BÊN TRÁI (HÌNH ẢNH, MÔ TẢ, THÔNG SỐ, ĐÁNH GIÁ) --}}
                {{-- ============================================= --}}
                <div class="col-lg-8">
                    <div class="row g-4">

                        {{-- PHẦN HÌNH ẢNH SẢN PHẨM (Giữ nguyên) --}}
                        <div class="col-lg-6">
                            @if($product && $product->images->count() > 0)
                                <div id="productCarousel" class="carousel slide mb-3" data-bs-ride="carousel">
                                    <div class="carousel-inner rounded border" style="background-color: #f8f9fa;">
                                        @foreach($product->images as $index => $image)
                                            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                                <div class="ratio ratio-1x1">
                                                    <img src="{{ asset('storage/' . $image->image_path) }}" class="d-block w-100 h-100 rounded" style="object-fit: contain; object-position: center;" alt="{{ $product->name }} - ảnh {{ $index + 1 }}">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @if($product->images->count() > 1)
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

                                <div class="d-flex flex-wrap justify-content-center" style="gap: 10px;">
                                    @foreach($product->images as $index => $image)
                                        <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="{{ $index }}" class="{{ $loop->first ? 'active' : '' }} border rounded p-1" aria-current="{{ $loop->first ? 'true' : 'false' }}" aria-label="Slide {{ $index + 1 }}" style="width: 70px; height: 70px; background-color: #f8f9fa;">
                                            <img src="{{ asset('storage/' . $image->image_path) }}" class="d-block w-100 h-100 rounded" style="object-fit: contain;" alt="thumbnail {{ $index + 1 }}">
                                        </button>
                                    @endforeach
                                </div>
                            @else
                                <div class="ratio ratio-1x1 border rounded" style="background-color: #f8f9fa;">
                                    <img src="{{ asset('storage/product_images/default.jpg') }}" class="d-block w-100 h-100 rounded" style="object-fit: contain; object-position: center;" alt="Ảnh sản phẩm mặc định">
                                </div>
                            @endif
                        </div>

                        {{-- PHẦN THÔNG TIN SẢN PHẨM (Giữ nguyên) --}}
                        <div class="col-lg-6">
                            <h1 class="h3 fw-bold mb-3">{{ $product->name }}</h1>
                            <div class="d-flex align-items-center mb-3">
                                <div class="text-warning me-2">
                                    @php $rating = $product->reviews_avg_rating ?? 0; @endphp
                                    @foreach(range(1, 5) as $star)
                                        <i class="{{ $rating >= $star ? 'fas fa-star' : ($rating >= $star - 0.5 ? 'fas fa-star-half-alt' : 'far fa-star') }}"></i>
                                    @endforeach
                                </div>
                                <span class="text-muted">({{ $product->reviews_count ?? 0 }} đánh giá)</span>
                            </div>

                            <div class="mb-3">
                                @if($product->brand)
                                    <span class="badge bg-secondary me-2">Thương hiệu: {{ $product->brand->name }}</span>
                                @endif
                                @if($product->category)
                                    <span class="badge bg-info text-dark">Danh mục: {{ $product->category->name }}</span>
                                @endif
                            </div>

                            <div class="bg-light p-3 rounded mb-3">
                                <span class="text-muted text-decoration-line-through me-2">
                                    {{ number_format($product->price * 1.1, 0, ',', '.') }}₫
                                </span>
                                <strong class="h4 text-danger fw-bold">
                                    {{ number_format($product->price, 0, ',', '.') }}₫
                                </strong>
                            </div>

                            <p class="mb-3">Tình trạng:
                                <span class="{{ $product->stock > 0 ? 'text-success fw-semibold' : 'text-danger fw-semibold' }}">
                                    {{ $product->stock > 0 ? 'Còn hàng' : 'Hết hàng' }}
                                </span>
                            </p>

                            @if (Auth::check())
                                @if (empty(Auth::user()->phoneNumber) || empty(Auth::user()->email) || empty(Auth::user()->address))
                                    <a href="{{ route('general.users.edit', Auth::user()->id) }}" class="btn btn-warning btn-sm w-100 flex-grow-1">
                                        <i class="fas fa-user-edit"></i> Cập nhật thêm thông tin để có thể mua hàng
                                    </a>
                                @else
                                    @if($product->stock > 0)
                                        <form id="product-cart-form" action="{{ route('buyer.carts.add') }}" method="POST" class="d-flex flex-column gap-2">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                                            <div class="d-flex align-items-center gap-2">
                                                <label for="quantity" class="col-form-label me-2 mb-0">Số lượng:</label>
                                                <input type="number" name="quantity" id="quantity" class="form-control form-control-sm" value="1" min="1" max="{{ $product->stock }}" style="width: 100px;" required>
                                                <small class="text-muted ms-2">Tối đa: {{ $product->stock }}</small>
                                            </div>

                                            <button type="submit" class="btn btn-danger btn-lg w-100">
                                                <i class="fas fa-cart-plus me-2"></i> Thêm vào giỏ hàng
                                            </button>

                                            <button type="submit" class="btn btn-success btn-lg w-100" formaction="{{ route('buyer.carts.store') }}" formmethod="POST">
                                                <i class="fas fa-bolt me-2"></i> Mua ngay
                                            </button>
                                        </form>

                                        <script>
                                            (function() {
                                                const form = document.getElementById('product-cart-form');
                                                if (!form) return;
                                                const qtyInput = document.getElementById('quantity');
                                                const max = Number(@json($product->stock));
                                                form.addEventListener('submit', function(e) {
                                                    const qty = Number(qtyInput.value) || 0;
                                                    if (qty < 1 || qty > max) {
                                                        e.preventDefault();
                                                        alert('Số lượng không hợp lệ.');
                                                        return;
                                                    }
                                                    // disable buttons to prevent double submit
                                                    form.querySelectorAll('button[type="submit"]').forEach(b => b.disabled = true);
                                                });
                                            })();
                                        </script>
                                    @else
                                        <button class="btn btn-secondary btn-sm w-100" disabled>Hết hàng</button>
                                    @endif
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="btn btn-success btn-sm w-100">
                                    <i class="fas fa-cart-plus"></i> Đăng nhập để mua
                                </a>
                            @endif
                        </div>
                    </div>
                    @if(session()->has('success'))
                        <div class="col-12 mt-3">
                            <div class="alert alert-info">
                                {{ session('success') }}
                            </div>
                        </div>
                    @endif

                    {{-- PHẦN MÔ TẢ (Giữ nguyên) --}}
                    <div class="col-12 mt-4">
                        <div class="card shadow-sm border-0" style="border-radius: 16px;">
                            <div class="card-header bg-white py-3" style="border-radius: 16px 16px 0 0;">
                                <h5 class="mb-0">Mô tả sản phẩm</h5>
                            </div>
                            <div class="card-body product-description">
                                {!! nl2br($product->description) !!}
                            </div>
                        </div>
                    </div>

                    {{-- PHẦN THÔNG SỐ (Giữ nguyên) --}}
                    <div class="col-12 mt-4">
                        <div class="card shadow-sm border-0" style="border-radius: 16px;">
                            <div class="card-header bg-white py-3" style="border-radius: 16px 16px 0 0;">
                                <h5 class="mb-0">Thông số kỹ thuật</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped table-bordered">
                                    <tbody>
                                    @php
                                        $productData = $product->attributes ?? [];
                                        $hasData = false;
                                    @endphp

                                    @foreach($specs as $spec)
                                        @php
                                            $value = $productData[$spec->id] ?? null;
                                            if (is_null($value)) {
                                            $value = $productData[(string)$spec->id] ?? null;
                                            }
                                            if (is_null($value)) {
                                            $value = $productData[$spec->name] ?? null;
                                            }
                                        @endphp

                                        @if(!empty($value))
                                            @php $hasData = true; @endphp
                                            <tr>
                                                <th style="width: 30%;">
                                                    {{ $spec->name }}
                                                </th>
                                                <td>
                                                    {{ $value }}
                                                    @if(!empty($spec->unit))
                                                        <small class="text-muted">({{ $spec->unit }})</small>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach

                                    @if(!$hasData)
                                        <tr>
                                            <td colspan="2" class="text-center text-muted">
                                                Chưa cập nhật thông số kỹ thuật.
                                            </td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- ============================================= --}}
                    {{-- PHẦN ĐÁNH GIÁ SẢN PHẨM (CÓ LOGIC ẨN/HIỆN) --}}
                    {{-- ============================================= --}}
                    <div class="col-12 mt-4" id="reviews">
                        <div class="card shadow-sm border-0" style="border-radius: 16px;">
                            <div class="card-header bg-white py-3" style="border-radius: 16px 16px 0 0;">
                                <h5 class="mb-0">Đánh giá sản phẩm</h5>
                            </div>
                            <div class="card-body">
                                @if ($product->reviews_count > 0)
                                    <div class="row align-items-center mb-4">
                                        <div class="col-md-3 text-center border-end">
                                            <h1 class="display-5 text-danger fw-bold mb-0">
                                                {{ number_format($product->reviews_avg_rating, 1) }}<span class="h4 text-muted">/5</span>
                                            </h1>
                                            <div class="text-warning mb-2">
                                                @php $avg_rating = $product->reviews_avg_rating; @endphp
                                                @foreach(range(1, 5) as $star)
                                                    <i class="{{ $avg_rating >= $star ? 'fas fa-star' : ($avg_rating >= $star - 0.5 ? 'fas fa-star-half-alt' : 'far fa-star') }}"></i>
                                                @endforeach
                                            </div>
                                            <span class="text-muted">({{ $product->reviews_count }} đánh giá)</span>
                                        </div>
                                        <div class="col-md-9">
                                            <p class="text-muted">Hiển thị các đánh giá mới nhất của sản phẩm.</p>
                                        </div>
                                    </div>
                                    <hr>
                                @endif

                                <div class="review-list">
                                    @forelse ($reviews as $review)
                                        {{-- 1. LOGIC GIAO DIỆN: Thêm class làm mờ nếu đang ẩn --}}
                                        <div class="d-flex mb-4 {{ $review->is_hidden ? 'opacity-75 bg-light p-3 rounded border border-warning' : '' }}">
                                            <div class="flex-shrink-0 me-3">
                                                @if ($review->buyer && $review->buyer->img)
                                                    <img src="{{ asset('storage/' . $review->buyer->img) }}" alt="{{ $review->buyer->name }}" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <img src="{{ asset('storage/profile_images/default.jpg') }}" alt="{{ $review->buyer->name ?? 'User' }}" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                                                @endif
                                            </div>
                                            
                                            <div class="flex-grow-1">
                                                {{-- Header đánh giá: Tên + Badge Ẩn + Nút Thao tác --}}
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="mt-0 mb-1 fw-bold">
                                                            {{ $review->buyer->name ?? 'Người dùng' }}
                                                            
                                                            {{-- 2. LOGIC GIAO DIỆN: Badge thông báo nếu đang ẩn --}}
                                                            @if($review->is_hidden)
                                                                <span class="badge bg-warning text-dark ms-2" style="font-size: 0.75rem;">
                                                                    <i class="fas fa-eye-slash me-1"></i> Đang ẩn
                                                                </span>
                                                            @endif
                                                        </h6>
                                                        
                                                        <div class="text-warning mb-1">
                                                            @foreach(range(1, 5) as $star)
                                                                <i class="fas fa-star" style="color: {{ $review->rating >= $star ? '#ffc107' : '#e0e0e0' }};"></i>
                                                            @endforeach
                                                        </div>
                                                    </div>

                                                    {{-- 3. LOGIC THAO TÁC: Nút Ẩn/Hiện (Chỉ hiện cho chính chủ) --}}
                                                    @if(auth()->check() && auth()->id() === $review->buyer_id)
                                                        <form action="{{ route('reviews.toggleHidden', $review->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm {{ $review->is_hidden ? 'btn-outline-success' : 'btn-outline-secondary' }}" 
                                                                    title="{{ $review->is_hidden ? 'Hiện đánh giá này cho mọi người' : 'Ẩn đánh giá này đi' }}">
                                                                @if($review->is_hidden)
                                                                    <i class="fas fa-eye"></i> Hiện lại
                                                                @else
                                                                    <i class="fas fa-eye-slash"></i> Ẩn
                                                                @endif
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>

                                                <p class="mb-1" style="white-space: pre-wrap;">{{ $review->comment }}</p>
                                                <small class="text-muted">{{ $review->created_at->format('d/m/Y \l\ú\c H:i') }}</small>

                                                @if ($review->reply)
                                                    <div class="d-flex mt-3 ms-4">
                                                        <div class="flex-shrink-0 me-3">
                                                            @if ($seller->img)
                                                                <img src="{{ asset('storage/' . $seller->img) }}" alt="{{ $seller->name }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                                            @else
                                                                <img src="{{ asset('storage/profile_images/default.jpg') }}" alt="{{ $seller->name }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                                            @endif
                                                        </div>
                                                        <div class="flex-grow-1 bg-light rounded p-3">
                                                            <h6 class="mt-0 mb-1 fw-bold">{{ $seller->name }}
                                                                <span class="badge bg-secondary fw-normal ms-1">Người bán</span>
                                                            </h6>
                                                            <p class="mb-0" style="white-space: pre-wrap;">{{ $review->reply }}</p>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if ($review->buyer_additional_feedback)
                                                    <div class="d-flex mt-3 ms-4">
                                                        <div class="flex-shrink-0 me-3">
                                                            @if ($review->buyer && $review->buyer->img)
                                                                <img src="{{ asset('storage/' . $review->buyer->img) }}" alt="{{ $review->buyer->name }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                                            @else
                                                                <img src="{{ asset('storage/profile_images/default.jpg') }}" alt="{{ $review->buyer->name ?? 'User' }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                                            @endif
                                                        </div>
                                                        <div class="flex-grow-1 bg-white border rounded p-3">
                                                            <h6 class="mt-0 mb-1 fw-bold">{{ $review->buyer->name ?? 'Người dùng' }}
                                                                <span class="badge bg-info text-dark fw-normal ms-1">Phản hồi bổ sung</span>
                                                            </h6>
                                                            <p class="mb-0" style="white-space: pre-wrap;">{{ $review->buyer_additional_feedback }}</p>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        @if (!$loop->last)
                                            <hr class="my-3">
                                        @endif
                                    @empty
                                        <div class="text-center py-4">
                                            <i class="fas fa-comment-dots fa-2x text-muted mb-2"></i>
                                            <p class="text-muted mb-0">Chưa có đánh giá nào cho sản phẩm này.</p>
                                        </div>
                                    @endforelse
                                </div>

                                <div class="mt-4 d-flex justify-content-center">
                                    {{ $reviews->links('pagination::bootstrap-5') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- ============================================= --}}
                    {{-- KẾT THÚC PHẦN ĐÁNH GIÁ SẢN PHẨM --}}
                    {{-- ============================================= --}}

                </div> {{-- Hết <div class="col-lg-8"> --}}

                {{-- ============================================= --}}
                {{-- CỘT BÊN PHẢI (SELLER & SẢN PHẨM LIÊN QUAN) --}}
                {{-- ============================================= --}}
                <div class="col-lg-4">

                    {{-- 1. THÔNG TIN NGƯỜI BÁN (GIAO DIỆN HIỆN ĐẠI) --}}
                    <div class="modern-shop-card mb-4" style="text-align: left; height: auto;">
                        <div class="shop-card-banner" style="height: 60px; background: linear-gradient(120deg, #84fab0 0%, #8fd3f4 100%);"></div>

                        <div class="px-3 pb-3">
                            <div class="d-flex align-items-center" style="margin-top: -30px;">
                                <div class="shop-avatar-wrapper me-3">
                                    @if ($seller->img)
                                        <img src="{{ asset('storage/' . $seller->img) }}" alt="{{ $seller->name }}" class="shop-avatar-img" style="width: 70px; height: 70px; border: 3px solid #fff;">
                                    @else
                                        <img src="{{ asset('storage/profile_images/default.jpg') }}" alt="{{ $seller->name }}" class="shop-avatar-img" style="width: 70px; height: 70px; border: 3px solid #fff;">
                                    @endif
                                    <div class="verified-badge" style="width: 18px; height: 18px; font-size: 10px; bottom: 2px; right: 2px;">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>

                                <div class="mt-4 pt-1">
                                    <h6 class="shop-name mb-1 text-truncate" style="font-size: 1.1rem;">{{ $seller->name }}</h6>
                                    <a href="/shop/{{ $seller->id }}" class="btn btn-outline-primary btn-sm btn-shop-view" style="font-size: 0.8rem; padding: 4px 12px;">
                                        Xem Shop
                                    </a>
                                </div>
                            </div>

                            <hr class="my-3 text-muted opacity-25">

                            <div class="small text-muted">
                                <p class="mb-1"><i class="fas fa-map-marker-alt me-2 text-danger"></i> {{ $seller->address ?? 'Chưa cập nhật địa chỉ' }}</p>
                                <p class="mb-0"><i class="fas fa-calendar-alt me-2 text-primary"></i> Tham gia: {{ $seller->created_at->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- 2. SẢN PHẨM LIÊN QUAN --}}
                    <div class="card shadow-sm border-0 sticky-top" style="top: 20px; border-radius: 16px; overflow: hidden;">
                        <div class="card-header bg-white py-3 border-bottom-0">
                            <h5 class="mb-0 fw-bold">Sản phẩm liên quan</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @forelse ($relatedProducts as $related)
                                    <a href="{{ route('products.detail', ['id' => $related->id]) }}" class="list-group-item list-group-item-action p-3 border-bottom-0 border-top">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-3 position-relative">
                                                <img src="{{ $related->images->isNotEmpty() ? asset('storage/' . $related->images->first()->image_path) : asset('storage/product_images/default.jpg') }}" alt="{{ $related->name }}" class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                                @if($related->stock == 0)
                                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary" style="font-size: 0.6rem;">Hết</span>
                                                @endif
                                            </div>

                                            <div class="flex-grow-1 overflow-hidden">
                                                <h6 class="mb-1 text-truncate text-dark fw-semibold" style="font-size: 0.95rem;">{{ $related->name }}</h6>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="text-danger fw-bold">{{ number_format($related->price, 0, ',', '.') }}₫</span>
                                                    <small class="text-muted" style="font-size: 0.75rem;">
                                                        <i class="fas fa-eye me-1"></i> Xem ngay
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="p-4 text-center text-muted">
                                        <i class="fas fa-box-open mb-2"></i><br>
                                        Không tìm thấy sản phẩm liên quan.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                </div>

            </div> {{-- Hết <div class="row g-5"> --}}

        </div> {{-- Hết <div class="card-body"> --}}
    </div> {{-- Hết <div class="card"> --}}
</div> {{-- Hết <div class="container"> --}}
@endsection