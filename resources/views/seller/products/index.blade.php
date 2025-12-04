@extends('layouts.SellerDashBoard')
@section('title', 'Danh sách sản phẩm') {{-- Thêm title --}}

@section('content')
<div class="container">
    <div class="page-header mt-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div class="mb-3 d-flex flex-wrap">
                <a href="{{ route('products.import') }}" class="btn btn-primary mr-2 mb-2 flex-fill text-nowrap">
                    <i class="fas fa-plus mr-2"></i>Thêm sản phẩm bằng File excel
                </a>
                <a href="{{ route('seller.products.create') }}" class="btn btn-primary mb-2 flex-fill text-nowrap">
                    <i class="fas fa-plus mr-2"></i>Thêm sản phẩm
                </a>
                <a href="{{ route('seller.stock.history') }}" class="btn btn-primary mb-2 flex-fill text-nowrap">
                    <i class="fas fa-plus mr-2"></i>Nhập/xuất kho
                </a>
            </div>

            <div class="mb-3">
                <a href="{{ route('seller.dashboard') }}" class="btn btn-primary mb-2 flex-fill text-nowrap">
                    <i class="fas fa-home me-2"></i>Quay lại
                </a>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('seller.products.index') }}" method="GET">
                <div class="row g-3 align-items-end"> {{-- Giữ align-items-end --}}

                    {{-- Tìm tên (OK) --}}
                    <div class="col-md-3"> {{-- Giảm cột --}}
                        <label for="search" class="form-label fw-bold">Tìm theo tên</label>
                        <input type="text" class="form-control" id="search" name="search" placeholder="Nhập tên sản phẩm..." value="{{ request('search') }}">
                    </div>

                    {{-- Trạng thái (OK) --}}
                    <div class="col-md-2">
                        <label for="status" class="form-label fw-bold">Trạng thái</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Tất cả</option>
                            <option value="Pending" @if(request('status')=='Pending' ) selected @endif>Pending</option>
                            <option value="Approved" @if(request('status')=='Approved' ) selected @endif>Approved</option>
                            <option value="Hidden" @if(request('status')=='Hidden' ) selected @endif>Hidden</option>
                            <option value="Rejected" @if(request('status')=='Rejected' ) selected @endif>Rejected</option>
                        </select>
                    </div>

                    {{-- Danh mục (OK) --}}
                    <div class="col-md-2">
                        <label for="category" class="form-label fw-bold">Danh mục</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">Tất cả</option>
                            @isset($categories)
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" @if(request('category')==$category->id) selected @endif>
                                {{ $category->name }}
                            </option>
                            @endforeach
                            @endisset
                        </select>
                    </div>

                    {{-- *** THÊM BỘ LỌC THƯƠNG HIỆU *** --}}
                    <div class="col-md-2">
                        <label for="brand_id" class="form-label fw-bold">Thương hiệu</label>
                        <select class="form-select" id="brand_id" name="brand_id"> {{-- Dùng name="brand_id" --}}
                            <option value="">Tất cả</option>
                            @isset($brands)
                            @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" @if(request('brand_id')==$brand->id) selected @endif>
                                {{ $brand->name }}
                            </option>
                            @endforeach
                            @endisset
                        </select>
                    </div>

                    {{-- Nút lọc (OK) --}}
                    <div class="col-md-3"> {{-- Tăng cột --}}
                        <div class="d-flex">
                            <button type="submit" class="btn btn-primary w-100 me-2">
                                <i class="fas fa-filter me-1"></i> Lọc
                            </button>
                            <a href="{{ route('seller.products.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-times me-1"></i> Xóa lọc
                            </a>
                        </div>
                    </div>
                </div> {{-- Đóng thẻ div class="row g-3 align-items-end" --}}
            </form>
        </div>
    </div>

    {{-- Thông báo thành công (OK) --}}
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @php
    // Tạo mảng thứ tự ưu tiên trạng thái
    $statusOrder = ['Pending', 'Approved', 'Hidden', 'Rejected'];

    // Sắp xếp collection theo thứ tự ưu tiên
    $sortedProducts = $products->sortBy(function($product) use ($statusOrder) {
    $index = array_search($product->status, $statusOrder);
    return $index !== false ? $index : 99; // những trạng thái không có trong mảng sẽ đứng cuối
    });
    @endphp

    @if($sortedProducts && $sortedProducts->count() > 0)
    <div class="row gy-4">
        @foreach($sortedProducts as $product)
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <div class="card h-100 product-card position-relative shadow-sm">

                {{-- Badge trạng thái (OK) --}}
                @php
                $status = $product->status;
                $badgeClass = match($status) {
                'Pending' => 'bg-secondary',
                'Approved' => 'bg-success',
                'Hidden' => 'bg-dark',
                'Rejected' => 'bg-danger',
                'Deleted' => 'bg-muted',
                default => 'bg-light text-dark'
                };
                @endphp
                <span class="status-badge badge {{ $badgeClass }} position-absolute top-0 start-0 m-2 p-2">
                    {{ ucfirst($status) }}
                </span>

                {{-- Ảnh (OK) --}}
                <img src="{{ $product->images->isNotEmpty() ? asset('storage/' . $product->images->first()->image_path) : asset('storage/product_images/default.jpg') }}" alt="{{ $product->name }}" class="card-img-top product-img">

                <div class="card-body product-body d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="card-title product-title">{{ $product->name }}</h5>
                        {{-- Danh mục (OK) --}}
                        <p class="product-meta small mb-2"><i class="fas fa-box me-2"></i> Danh mục:
                            <span class="fw-semibold text-dark">{{ $product->category->name ?? 'Chưa phân loại' }}</span>
                        </p>
                        <div class="product-meta small mb-2">
                            {{-- Tồn kho (OK) --}}
                            <div><i class="fas fa-box me-2"></i>Tồn kho: {{ $product->stock }}</div>
                            {{-- *** SỬA THƯƠNG HIỆU Ở ĐÂY *** --}}
                            <div><i class="fas fa-tag me-2"></i>Thương hiệu: {{ $product->brand->name ?? 'Chưa có' }}</div> {{-- Sửa thành $product->brand->name --}}
                        </div>

                        {{-- Giá (OK) --}}
                        <div class="product-price fw-bold text-primary mb-3">
                            {{ number_format($product->price, 0, ',', '.') }}₫
                        </div>
                    </div>

                    {{-- Nút Sửa/Xóa (OK) --}}
                    <div class="mt-auto">
                        <a href="{{ route('products.detail', ['id' => $product->id]) }}" class="btn btn-info w-100 mb-2">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('seller.products.edit', $product->id) }}" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-edit me-2"></i>Sửa
                        </a>

                        @if($product->status == 'Approved')
                        <form action="{{ route('seller.products.hidden', $product->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="fas fa-trash me-2"></i>Ẩn
                            </button>
                        </form>
                        @elseif($product->status == 'Pending')
                        <div class="alert alert-info text-center mb-0">
                            Sản phẩm đang chờ duyệt
                        </div>
                        @elseif($product->status == 'Rejected')
                        <div class="alert alert-danger text-center mb-0">
                            Sản phẩm bị từ chối
                        </div>
                        @elseif($product->status == 'Hidden' && $product->previous_status == 'Approved')
                        <form action="{{ route('seller.products.restore', $product->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-undo me-2"></i>Hiện
                            </button>
                        </form>
                        @else
                        <div class="alert alert-secondary text-center mb-0">
                            Sản phẩm bị ẩn bởi Admin
                        </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    {{-- Thông báo rỗng (OK) --}}
    <div class="empty-state text-center my-5">
        <i class="fas fa-box-open fa-3x mb-3 text-muted"></i>
        <h4>Chưa có sản phẩm nào</h4>
        <p>Hãy thêm sản phẩm đầu tiên của bạn để bắt đầu bán hàng</p>
        <a href="{{ route('seller.products.create') }}" class="btn btn-success mt-3">
            <i class="fas fa-plus me-2"></i>Thêm sản phẩm đầu tiên
        </a>
    </div>
    @endif

</div> {{-- Đóng thẻ div class="container" --}}
@endsection
