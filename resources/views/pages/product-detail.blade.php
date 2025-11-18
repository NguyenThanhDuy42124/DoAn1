@extends('layouts.app')

{{-- Tiêu đề trang (Lấy tự động từ tên sản phẩm) --}}
@section('title', $product->name)

@section('content')
<div class="container mb-5">
    <div class="card shadow-sm border-0">
        @auth
        @php
        $restrictedStatuses = ['Pending', 'Rejected', 'Hidden'];
        @endphp

        @if(in_array($product->status, $restrictedStatuses) &&
        (auth()->user()->role === 'admin' || auth()->user()->id === $seller->id))
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
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

                            {{-- SỬA LỖI: Thêm action cho form (route cart.add phải tồn tại) --}}
                            <form action="" method="POST">
                                @csrf
                                <div class="d-flex mb-3">
                                    <label for="quantity" class="col-form-label me-2">Số lượng:</label>
                                    <input type="number" name="quantity" id="quantity" class="form-control" value="1" min="1" max="{{ $product->stock }}" style="width: 80px;">
                                </div>

                                @if ($product->stock > 0)
                                <button type="submit" class="btn btn-danger btn-lg w-100 mb-2">
                                    <i class="fas fa-cart-plus me-2"></i> Thêm vào giỏ hàng
                                </button>
                                <button type="submit" name="buy_now" value="1" class="btn btn-success btn-lg w-100">
                                    Mua ngay
                                </button>
                                @else
                                <button type="button" class="btn btn-secondary btn-lg w-100" disabled>
                                    <i class="fas fa-times-circle me-2"></i> Hết hàng
                                </button>
                                @endif
                            </form>
                        </div>
                    </div>

                    {{-- PHẦN MÔ TẢ (Giữ nguyên) --}}
                    <div class="col-12 mt-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0">Mô tả sản phẩm</h5>
                            </div>
                            <div class="card-body product-description">
                                {!! nl2br($product->description) !!}
                            </div>
                        </div>
                    </div>

                    {{-- PHẦN THÔNG SỐ (Giữ nguyên) --}}
                    <div class="col-12 mt-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0">Thông số kỹ thuật</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped table-bordered">
                                    <tbody>
                                        @if(is_array($product->attributes) && count($product->attributes) > 0)
                                        @foreach($product->attributes as $key => $value)
                                        <tr>
                                            <th style="width: 30%;">{{ $key }}</th>
                                            <td>{{ $value }}</td>
                                        </tr>
                                        @endforeach
                                        @else
                                        <tr>
                                            <td colspan="2" class="text-center">Chưa cập nhật thông số kỹ thuật.</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- PHẦN ĐÁNH GIÁ SẢN PHẨM (ĐÃ CHỈNH SỬA) --}}
                    <div class="col-12 mt-4" id="reviews">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0">Đánh giá sản phẩm</h5>
                            </div>
                            <div class="card-body">

                                {{-- Phần Tóm tắt Đánh giá --}}
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

                                {{-- Danh sách Đánh giá --}}
                                <div class="review-list">
                                    @forelse ($reviews as $review)
                                    <div class="d-flex mb-4">

                                        {{-- AVATAR NGƯỜI MUA (Đã cập nhật theo yêu cầu) --}}
                                        <div class="flex-shrink-0 me-3">
                                            @if ($review->buyer && $review->buyer->img)
                                            {{-- Nếu người mua có ảnh, hiển thị ảnh của họ --}}
                                            <img src="{{ asset('storage/' . $review->buyer->img) }}" alt="{{ $review->buyer->name }}" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                            {{-- Nếu không có, hiển thị ảnh default --}}
                                            <img src="{{ asset('storage/profile_images/default.jpg') }}" alt="{{ $review->buyer->name ?? 'User' }}" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                                            @endif
                                        </div>

                                        {{-- Nội dung đánh giá --}}
                                        <div class="flex-grow-1">
                                            <h6 class="mt-0 mb-1 fw-bold">{{ $review->buyer->name ?? 'Người dùng' }}</h6>

                                            {{-- Sao --}}
                                            <div class="text-warning mb-1">
                                                @foreach(range(1, 5) as $star)
                                                <i class="fas fa-star" style="color: {{ $review->rating >= $star ? '#ffc107' : '#e0e0e0' }};"></i>
                                                @endforeach
                                            </div>

                                            {{-- Bình luận --}}
                                            <p class="mb-1" style="white-space: pre-wrap;">{{ $review->comment }}</p>
                                            <small class="text-muted">{{ $review->created_at->format('d/m/Y \l\ú\c H:i') }}</small>

                                            {{-- ============================================= --}}
                                            {{-- BẮT ĐẦU CHỈNH SỬA: PHẢN HỒI CỦA SELLER --}}
                                            {{-- ============================================= --}}
                                            @if ($review->reply)
                                            <div class="d-flex mt-3 ms-4"> {{-- Thụt lề --}}
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
                                            {{-- ============================================= --}}
                                            {{-- KẾT THÚC CHỈNH SỬA: PHẢN HỒI CỦA SELLER --}}
                                            {{-- ============================================= --}}


                                            {{-- ============================================= --}}
                                            {{-- BẮT ĐẦU CHỈNH SỬA: PHẢN HỒI BỔ SUNG CỦA BUYER --}}
                                            {{-- ============================================= --}}
                                            @if ($review->buyer_additional_feedback)
                                            <div class="d-flex mt-3 ms-4"> {{-- Thụt lề --}}
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
                                            {{-- ============================================= --}}
                                            {{-- KẾT THÚC CHỈNH SỬA: PHẢN HỒI BỔ SUNG CỦA BUYER --}}
                                            {{-- ============================================= --}}

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
                                    @endforelse {{-- <-- SỬA LỖI CÚ PHÁP (từ @empty thành @endforelse) --}}
                                </div>

                                {{-- Phân trang cho Reviews --}}
                                <div class="mt-4 d-flex justify-content-center">
                                    {{ $reviews->links('pagination::bootstrap-5') }}
                                </div>
                            </div>
                        </div>
                    </div>

                </div> {{-- Hết <div class="col-lg-8"> --}}

                {{-- ============================================= --}}
                {{-- CỘT BÊN PHẢI (SELLER & SẢN PHẨM LIÊN QUAN) --}}
                {{-- ============================================= --}}
                <div class="col-lg-4">

                    {{-- ============================================= --}}
                    {{-- BỔ SUNG: PHẦN THÔNG TIN NGƯỜI BÁN (VỊ TRÍ MỚI) --}}
                    {{-- ============================================= --}}
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white py-3">
                            <h4 class="mb-0 fw-bold">Thông tin Người bán</h4>
                        </div>
                        <div class="card-body d-flex align-items-center">
                            {{-- Sửa: Thêm logic ảnh default cho seller --}}
                            @if ($seller->img)
                            <img src="{{ asset('storage/' . $seller->img) }}" alt="{{ $seller->name }}" class="rounded-circle border me-4" style="width: 80px; height: 80px; object-fit: cover;">
                            @else
                            <img src="{{ asset('storage/profile_images/default.jpg') }}" alt="{{ $seller->name }}" class="rounded-circle border me-4" style="width: 80px; height: 80px; object-fit: cover;">
                            @endif

                            <div>
                                <h5 class="fw-semibold mb-1">{{ $seller->name }}</h5>
                                <p class="mb-2 text-muted">Địa chỉ: {{ $seller->address ?? 'Chưa cập nhật' }}</p>
                                <a href="/shop/{{ $seller->id }}" class="btn btn-outline-primary btn-sm">
                                    Xem cửa hàng
                                </a>
                            </div>
                        </div>
                    </div>
                    {{-- ============================================= --}}
                    {{-- KẾT THÚC BỔ SUNG --}}
                    {{-- ============================================= --}}


                    {{-- SẢN PHẨM LIÊN QUAN (Nằm dưới Seller) --}}
                    <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0">Sản phẩm liên quan</h5>
                        </div>
                        <div class="card-body">

                            {{-- Vòng lặp sản phẩm liên quan --}}
                            @forelse ($relatedProducts as $related)
                            <div class="d-flex align-items-center mb-3">

                                {{-- SỬA LỖI: Phải sử dụng $related cho hình ảnh --}}
                                <img src="{{ $related->images->isNotEmpty() ?
                         asset('storage/' . $related->images->first()->image_path) :
                         asset('storage/product_images/default.jpg') }}" alt="{{ $related->name }}" {{-- SỬA LỖI: Dùng tên của $related --}} class="img-fluid rounded me-3" {{-- Thêm lại me-3 (margin) để ảnh không dính vào chữ --}} style="width: 60px; height: 60px; object-fit: cover;">

                                <div>
                                    {{-- SỬA LỖI: Link phải trỏ đến $related->id --}}
                                    <a href="{{ route('products.detail', ['id' => $related->id]) }}" class="text-decoration-none text-dark fw-semibold d-block">
                                        {{ $related->name }}
                                    </a>
                                    <strong class="text-danger">{{ number_format($related->price, 0, ',', '.') }}₫</strong>
                                </div>
                            </div>
                            @empty
                            <p class="text-muted">Không tìm thấy sản phẩm liên quan.</p>
                            @endforelse

                        </div>
                    </div>
                </div>

            </div> {{-- Hết <div class="row g-5"> (Hàng chính) --}}

            {{-- (Đã xóa khối Seller ở cuối) --}}

        </div> {{-- Hết <div class="card-body"> --}}
    </div> {{-- Hết <div class="card"> --}}
</div> {{-- Hết <div class="container"> --}}
@endsection
