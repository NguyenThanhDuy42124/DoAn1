
    <title>Danh sách sản phẩm</title>
    @extends('layouts.SellerDashBoard')
    @section('content')
    <div class="container">
     <div class="page-header mt-4">
    <div class="d-flex justify-content-between align-items-center">
        
        <div class="mb-3 d-flex">
            <a href="{{ route('seller.products.import.form') }}" class="btn btn-primary mr-2">
                <i class="fas fa-plus mr-2"></i>Thêm sản phẩm bằng File excel
            </a>
            <a href="{{ route('seller.products.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i>Thêm sản phẩm
            </a>
        </div>
        
              <div class="mb-3">
            <a href="{{ route('seller.dashboard') }}" class="btn btn-primary">
                <i class="fas fa-home me-2"></i>Quay lại
            </a>
        </div>
    </div>
</div>

@if($products && count($products) > 0)
    <div class="row gy-4">
        @foreach($products as $product)
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card h-100 product-card position-relative shadow-sm">
                    @if($product->status === 'active')
                        <span class="status-badge status-active">Đang bán</span>
                    @else
                        <span class="status-badge status-inactive">Ngừng bán</span>
                    @endif

                    <img src="{{ $product->images->isNotEmpty() ? asset('storage/' . $product->images->first()->image_path) : asset('storage/product_images/default.jpg') }}"
                         alt="{{ $product->name }}"
                         class="card-img-top product-img">

                    <div class="card-body product-body d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="card-title product-title">{{ $product->name }}</h5>

                            <div class="product-meta small mb-2">
                                <div><i class="fas fa-box me-2"></i>Tồn kho: {{ $product->stock }}</div>
                                <div><i class="fas fa-tag me-2"></i>Thương hiệu: {{ $product->brand ?? 'Chưa có' }}</div>
                            </div>

                            <div class="product-price fw-bold text-primary mb-3">
                                {{ number_format($product->price, 0, ',', '.') }}₫
                            </div>
                        </div>

                        <div class="mt-auto">
                            <a href="{{ route('seller.products.edit', $product->id) }}" class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-edit me-2"></i>Sửa
                            </a>

                            <form action="{{ route('seller.products.destroy', $product->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="fas fa-trash me-2"></i>Xóa
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="empty-state text-center my-5">
        <i class="fas fa-box-open fa-3x mb-3 text-muted"></i>
        <h4>Chưa có sản phẩm nào</h4>
        <p>Hãy thêm sản phẩm đầu tiên của bạn để bắt đầu bán hàng</p>
        <a href="{{ route('seller.products.create') }}" class="btn btn-success mt-3">
            <i class="fas fa-plus me-2"></i>Thêm sản phẩm đầu tiên
        </a>
    </div>
@endif

    </div>
    @endsection
   