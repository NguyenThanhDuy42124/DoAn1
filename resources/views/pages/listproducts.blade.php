 
@extends('layouts.app')
@section('title', 'Danh sách sản phẩm')
    @section('content')
   
<!-- Header Section -->
<div class="page-header">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-md-6">
        <h1 class="display-5 fw-bold"><i class="fas fa-boxes me-2"></i>Ten Shop</h1>
      </div>
      <div class="col-md-6 text-md-end">
       
        <a href="#" class="btn btn-light"><i class="fas fa-shopping-cart me-2"></i>Giỏ hàng (3)</a>
      </div>
    </div>
  </div>
</div>

<!-- Filter Section -->
<div class="container">
  <div class="filter-section">
    <div class="row">
      <div class="col-md-4">
        <h6><i class="fas fa-dollar-sign me-2"></i>Khoảng giá</h6>
        <select class="form-select">
          <option selected>Tất cả</option>
          <option>Dưới 5 triệu</option>
          <option>5 - 10 triệu</option>
          <option>10 - 20 triệu</option>
          <option>Trên 20 triệu</option>
        </select>
      </div>
      <div class="col-md-4">
        <h6><i class="fas fa-tag me-2"></i>Thương hiệu</h6>
        <select class="form-select">
          <option selected>Tất cả</option>
          <option>Apple</option>
          <option>Samsung</option>
          <option>Xiaomi</option>
          <option>OPPO</option>
        </select>
      </div>
      <div class="col-md-4">
        <h6><i class="fas fa-sliders-h me-2"></i>Tùy chọn</h6>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="discount" checked>
          <label class="form-check-label" for="discount">Đang giảm giá</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="stock" checked>
          <label class="form-check-label" for="stock">Còn hàng</label>
        </div>
        <button class="btn btn-primary btn-sm mt-2">Áp dụng</button>
      </div>
    </div>
  </div>
</div>

<!-- Products Section -->
<!-- Products Section -->
<div class="container">
  <div class="row g-4">
    @foreach($products as $product)
      <div class="col-md-4">
        <div class="card product-card">
          <span class="badge-discount">Discount</span>
          <img src="https://images.unsplash.com/photo-1598327105666-5b89351aff97?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" 
               class="product-img" alt="{{ $product->name }}">
          <div class="card-body">
            <h5 class="card-title">{{ $product->name }}</h5>  
            <p class="card-text text-muted">{{ Str::limit($product->description, 100) }}</p>
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <span class="product-price">{{ number_format($product->price, 0, ',', '.') }}₫</span>
              </div>
              <div class="d-flex">
                <button class="btn btn-outline-primary btn-sm me-2" data-bs-toggle="modal" 
                        data-bs-target="#detailModal-{{ $product->id }}">
                  <i class="fas fa-eye"></i>
                </button>

                @if (Auth::check())
                  @if (empty(Auth::user()->phoneNumber) || empty(Auth::user()->email))
                    <a href="{{ route('users.edit', Auth::user()->id) }}" class="btn btn-warning btn-sm">
                      <i class="fas fa-user-edit"></i> Cập nhật thông tin
                    </a>
                  @else
                    @if($product->stock > 0)
                      <form action="{{ route('buyer.carts.store') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn btn-success btn-sm">
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
    @endforeach
  </div>
</div>

  </div>
  
  <!-- Pagination -->

</div>

<!-- Modal: detail -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i>Chi tiết sản phẩm</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <img src="https://images.unsplash.com/photo-1598327105666-5b89351aff97?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" class="img-fluid rounded" alt="iPhone 14 Pro Max">
          </div>
          <div class="col-md-6">
            <h4>iPhone 14 Pro Max</h4>
            <div class="d-flex align-items-center mb-3">
              <span class="product-price me-2">28.990.000₫</span>
              <span class="text-muted text-decoration-line-through">32.990.000₫</span>
              <span class="badge bg-danger ms-2">-12%</span>
            </div>
            <p>iPhone 14 Pro Max mang đến trải nghiệm đỉnh cao với hiệu năng mạnh mẽ, camera chuyên nghiệp và thiết kế sang trọng.</p>
            <ul class="list-unstyled">
              <li><i class="fas fa-microchip me-2"></i> Chip A16 Bionic</li>
              <li><i class="fas fa-mobile-alt me-2"></i> Màn hình 6.7 inch Super Retina XDR</li>
              <li><i class="fas fa-camera me-2"></i> Camera chính 48MP</li>
              <li><i class="fas fa-battery-full me-2"></i> Pin lên đến 29 giờ sử dụng</li>
            </ul>
            <div class="d-grid gap-2">
              <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#cartModal">
                <i class="fas fa-cart-plus me-2"></i>Thêm vào giỏ hàng
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal: add to cart -->
<div class="modal fade" id="cartModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-cart-plus me-2"></i>Thêm vào giỏ hàng</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form>
          <div class="mb-3">
            <label for="color" class="form-label">Chọn màu sắc</label>
            <select id="color" class="form-select">
              <option>Đỏ</option>
              <option>Xanh</option>
              <option>Đen</option>
              <option>Tím</option>
              <option>Vàng</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="quantity" class="form-label">Số lượng</label>
            <input type="number" id="quantity" class="form-control" value="1" min="1">
          </div>
          <div class="d-grid">
            <button type="submit" class="btn btn-success">Xác nhận thêm vào giỏ</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection
