<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>TechStore - Cửa hàng điện thoại</title>
    <style>
        :root {
            --primary: #0066cc;
            --secondary: #ff6600;
            --light-bg: #f8f9fa;
            --dark-text: #333;
            --border-radius: 12px;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fb;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        
        /* Navbar styling */
        .navbar {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 0.5rem 0;
        }
        
        .navbar-brand {
            font-weight: bold;
            font-size: 24px;
        }
        
        .navbar-brand span:first-child {
            color: var(--primary);
        }
        
        .navbar-brand span:last-child {
            color: var(--secondary);
        }
        
        .navbar-nav .nav-link {
            font-weight: 500;
            padding: 0.5rem 1rem;
            color: var(--dark-text);
        }
        
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: var(--primary);
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-radius: var(--border-radius);
        }
        
        .dropdown-item {
            padding: 0.5rem 1rem;
        }
        
        .dropdown-item:hover {
            background-color: #f8f9fa;
            color: var(--primary);
        }
        
        .form-control {
            border-radius: 20px;
            padding: 0.5rem 1rem;
        }
        
        /* Main content */
        main {
            flex: 1;
            padding: 2rem 0;
        }
        
        /* Footer styling */
        footer {
            background-color: #f1f1f1;
            color: #555;
            padding: 2rem 0;
            margin-top: auto;
            width: 100%;
        }
        
        footer h5 {
            font-weight: 600;
            color: var(--dark-text);
            margin-bottom: 1rem;
        }
        
        footer p {
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        
        /* Button styling */
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            border-radius: 6px;
        }
        
        .btn-primary:hover {
            background-color: #0052a3;
            border-color: #0052a3;
        }
        
        .btn-outline-primary {
            color: var(--primary);
            border-color: var(--primary);
            border-radius: 6px;
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary);
            color: white;
        }
        
        /* Card styling */
        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        
        /* Form styling */
        .form-control {
            border: 1px solid #dce1e6;
            border-radius: 6px;
            padding: 12px 15px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(0, 102, 204, 0.25);
        }
        
        /* Additional styles for consistency */
        .section-title {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eaeaea;
        }
        
        .text-primary {
            color: var(--primary) !important;
        }
        
        .bg-primary {
            background-color: var(--primary) !important;
        }
        
        .alert-primary {
            background-color: rgba(0, 102, 204, 0.1);
            border-color: rgba(0, 102, 204, 0.3);
            color: var(--primary);
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .navbar-nav {
                padding: 1rem 0;
            }
            
            .navbar-nav .nav-item {
                margin-bottom: 0.5rem;
            }
            
            footer .col-4 {
                width: 100%;
                margin-bottom: 1.5rem;
            }
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-light bg-light rounded fixed-top" aria-label="Eleventh navbar example">
  <div class="container-fluid">
    <!-- Logo -->
    <a class="navbar-brand fw-bold" href="/">
      <span class="text-primary">TEN</span><span class="text-dark">SHOP</span>
    </a>

    <!-- Nút thu gọn trên mobile -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample09"
      aria-controls="navbarsExample09" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Menu + Search -->
    <div class="collapse navbar-collapse" id="navbarsExample09">
      <!-- Menu trái -->
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" href="/san-pham">DANH SÁCH SẢN PHẨM</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="/khuyen-mai">KHUYẾN MÃI</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="/ho-tro">HỖ TRỢ</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="/gioi-thieu">GIỚI THIỆU</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="/tai-khoan">TÀI KHOẢN</a>
        </li>
      </ul>

      <!-- Form tìm kiếm phải -->
      <form class="d-flex ms-auto">
        <input class="form-control me-2" type="search" placeholder="Tìm kiếm sản phẩm..." aria-label="Search">
        <button class="btn btn-outline-primary" type="submit">
          <i class="fas fa-search"></i>
        </button>
      </form>
    </div>
  </div>
</nav>

    
    <main class="container flex-fill mt-5 pt-4">
        @yield('content')
    </main>
    
    <footer class="py-4 mt-auto">
        <div class="container">
            <div class="row">
                <!-- Cột 1 -->
                <div class="col-md-4 mt-3">
                    <h5>Tổng đài hỗ trợ miễn phí</h5>
                    <p class="mb-2">Mua hàng - bảo hành: 1800.0000 (7h30 - 22h00)</p>
                    <p class="mb-2">Khiếu nại: 1800.1111 (8h00 - 21h30)</p>
                </div>

                <!-- Cột 2 -->
                <div class="col-md-4 mt-3">
                    <h5>Thông tin và chính sách</h5>
                    <p class="mb-2">Mua hàng trả góp Online</p>
                    <p class="mb-2">Mua hàng trả góp bằng thẻ tín dụng</p>
                    <p class="mb-2">Chính sách giao hàng</p>
                </div>

                <!-- Cột 3 -->
                <div class="col-md-4 mt-3">
                    <h5>Dịch vụ và thông tin khác</h5>
                    <p class="mb-2">Khách hàng doanh nghiệp (B2B)</p>
                    <p class="mb-2">Ưu đãi thanh toán</p>
                    <p class="mb-2">Quy chế hoạt động</p>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-12 text-center">
                    <p class="mb-0">© COPYRIGHT.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>