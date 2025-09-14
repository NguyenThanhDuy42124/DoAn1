<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Danh sách sản phẩm</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary: #0066cc;
      --secondary: #ff6600;
      --light-bg: #f8f9fa;
      --dark-text: #333;
    }
    
    body {
      background-color: #f5f7fb;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #333;
    }
    
    .page-header {
      background: linear-gradient(to right, var(--primary), #004d99);
      color: white;
      padding: 40px 0;
      margin-bottom: 30px;
      border-radius: 0 0 20px 20px;
    }
    
    .product-card {
      transition: transform .2s;
      border: none;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
      height: 100%;
    }
    
    .product-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }
    
    .product-img {
      height: 200px;
      object-fit: cover;
      width: 100%;
    }
    
    .card-title {
      font-weight: 600;
      color: var(--dark-text);
      margin-bottom: 10px;
    }
    
    .product-price {
      color: var(--primary);
      font-weight: 700;
      font-size: 1.2rem;
    }
    
    .btn-primary {
      background-color: var(--primary);
      border-color: var(--primary);
      border-radius: 6px;
      font-weight: 500;
    }
    
    .btn-primary:hover {
      background-color: #0052a3;
      border-color: #0052a3;
    }
    
    .btn-success {
      background-color: var(--secondary);
      border-color: var(--secondary);
      border-radius: 6px;
      font-weight: 500;
    }
    
    .btn-success:hover {
      background-color: #e55a00;
      border-color: #e55a00;
    }
    
    .modal-header {
      background-color: var(--primary);
      color: white;
    }
    
    .filter-section {
      background: white;
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
      margin-bottom: 30px;
    }
    
    .pagination {
      justify-content: center;
      margin-top: 30px;
    }
    
    .page-link {
      color: var(--primary);
    }
    
    .page-item.active .page-link {
      background-color: var(--primary);
      border-color: var(--primary);
    }
    
    .empty-state {
      text-align: center;
      padding: 60px 20px;
      color: #6c757d;
    }
    
    .empty-state i {
      font-size: 4rem;
      margin-bottom: 15px;
      color: #dee2e6;
    }
    
    .badge-discount {
      position: absolute;
      top: 10px;
      right: 10px;
      background: var(--secondary);
      color: white;
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 600;
    }
  </style>
</head>
<body>

<!-- Header Section -->
<div class="page-header">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-md-6">
        <h1 class="display-5 fw-bold"><i class="fas fa-boxes me-2"></i>Danh Sách Sản Phẩm</h1>
      </div>
      <div class="col-md-6 text-md-end">
        <div class="d-inline-block me-3">
          <select class="form-select">
            <option selected>Sắp xếp theo</option>
            <option>Giá tăng dần</option>
            <option>Giá giảm dần</option>
            <option>Mới nhất</option>
            <option>Bán chạy nhất</option>
          </select>
        </div>
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
<div class="container">
  <div class="row g-4">
    
    <!-- Product 1 -->
    <div class="col-md-4">
      <div class="card product-card">
        <span class="badge-discount">-12%</span>
        <img src="https://images.unsplash.com/photo-1598327105666-5b89351aff97?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" class="product-img" alt="iPhone 14 Pro Max">
        <div class="card-body">
          <h5 class="card-title">tên</h5>  
          <p class="card-text text-muted">thông số nổi bật</p>
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <span class="product-price">giá</span>
              <span class="text-muted text-decoration-line-through ms-2">giá gốc</span>
            </div>
            <div class="d-flex">
              <button class="btn btn-outline-primary btn-sm me-2" data-bs-toggle="modal" data-bs-target="#detailModal">
                <i class="fas fa-eye"></i>
              </button>
              <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#cartModal">
                <i class="fas fa-cart-plus"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    
  <!-- Pagination -->
  <nav aria-label="Page navigation">
    <ul class="pagination">
      <li class="page-item disabled">
        <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Trước</a>
      </li>
      <li class="page-item active"><a class="page-link" href="#">1</a></li>
      <li class="page-item"><a class="page-link" href="#">2</a></li>
      <li class="page-item"><a class="page-link" href="#">3</a></li>
      <li class="page-item">
        <a class="page-link" href="#">Sau</a>
      </li>
    </ul>
  </nav>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>