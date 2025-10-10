@extends('layouts.app')
@section('title', 'Danh sách sản phẩm')
@section('content')
<div class="container">
   
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
          <span class="badge-discount">- X%</span>
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
                  @if (empty(Auth::user()->phoneNumber) || empty(Auth::user()->email || empty(Auth::user()->address)))
                    <a href="{{ route('general.users.edit', Auth::user()->id) }}" class="btn btn-warning btn-sm">
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
@endsection