<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    @vite(['resources/css/welcome.css', 'resources/js/app.js'])
    <title>Trang chủ</title>
</head>

<body>
    @extends('layouts.app')
    @section('content')
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Công Nghệ Mới Nhất - Giá Tốt Nhất</h1>
                <p class="hero-subtitle">Khám phá các sản phẩm điện thoại thông minh với ưu đãi đặc biệt chỉ có tại
                    tenshop</p>
                <a href="#products" class="btn btn-light btn-lg">Mua ngay <i class="fas fa-arrow-right ml-2"></i></a>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="container">
        <div class="section-title">
            <h2>CÁC THƯƠNG HIỆU NỔI BẬT</h2>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="category-card">
                    <div class="category-icon">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/2/24/Samsung_Logo.svg" alt="Samsung"
                            class="brand-logo">
                    </div>
                    <h3 class="category-title">SAMSUNG</h3>
                    <p>Điện thoại Samsung với nhiều dòng sản phẩm từ phổ thông đến cao cấp</p>
                    <a href="#" class="btn btn-outline-primary">Xem ngay</a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="category-card">
                    <div class="category-icon">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/f/fa/Apple_logo_black.svg" alt="Apple"
                            class="brand-logo" style="height: 40px;">
                    </div>
                    <h3 class="category-title">APPLE</h3>
                    <p>iPhone chính hãng với thiết kế sang trọng và hiệu năng vượt trội</p>
                    <a href="#" class="btn btn-outline-primary">Xem ngay</a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3 class="category-title">CÁC HÃNG KHÁC</h3>
                    <p>Điện thoại đến từ nhiều thương hiệu khác nhau, đa dạng mẫu mã và giá thành</p>
                    <a href="#" class="btn btn-outline-primary">Xem ngay</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="container" id="products">
        <div class="section-title">
            <h2>Sản Phẩm Nổi Bật</h2>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="product-card">
                    <!-- <img src="https://images.unsplash.com/photo-1598327105666-5b89351aff97?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" alt="iPhone 14 Pro Max" class="product-img">
                    <div class="product-body">
                        <h3 class="product-title">iPhone 14 Pro Max 128GB</h3>
                        <div class="product-price">
                            <span class="current-price">28.990.000₫</span>
                            <span class="old-price">32.990.000₫</span>
                            <span class="discount-badge">-12%</span>
                        </div>
                        <a href="#" class="btn btn-primary btn-block">Thêm vào giỏ</a>
                    </div> -->
                </div>
            </div>
            <div class="col-md-3">
                <div class="product-card">

                </div>
            </div>

            <div class="col-md-3">
                <div class="product-card">

                </div>
            </div>

            <div class="col-md-3">
                <div class="product-card">

                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="#" class="btn btn-outline-primary btn-lg">Xem tất cả sản phẩm</a>
        </div>
    </section>

    <!-- Promotional Banner -->
    <section class="container">
        <div class="promo-banner">
            <div class="promo-content">
                <div class="promo-text">
                    <h2 class="promo-title">Giảm giá đến 30% cho các sản phẩm Apple</h2>
                    <p>Ưu đãi đặc biệt chỉ diễn ra trong tháng này. Đừng bỏ lỡ!</p>
                </div>
                <a href="#" class="btn btn-light btn-lg">Xem ngay <i class="fas fa-arrow-right ml-2"></i></a>
            </div>
        </div>
    </section>
    @endsection
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>
</body>

</html>
