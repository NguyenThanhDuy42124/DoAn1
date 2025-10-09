   <nav class="navbar navbar-expand-lg navbar-light bg-light rounded fixed-top" aria-label="Eleventh navbar example">
        <div class="container-fluid d-flex">
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
                        <a class="nav-link active" href="/products">DANH SÁCH SẢN PHẨM</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/vouchers">KHUYẾN MÃI</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/ho-tro">HỖ TRỢ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/gioi-thieu">GIỚI THIỆU</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link active dropdown-toggle bg-light" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            TÀI KHOẢN
                        </a>

                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="/dashboard">Thông tin tài khoản</a>
                            <a class="dropdown-item" href="/login">Đăng nhập</a>
                            <a class="dropdown-item" href="/register">Đăng ký</a>
                        </div>
            </div>

            </li>  
            <a href="{{route('buyer.carts.index')}}" class="btn btn-light"><i class="fas fa-shopping-cart me-2"></i>Giỏ hàng {{ $totalItems }}</a>
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