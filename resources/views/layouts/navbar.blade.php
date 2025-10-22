 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
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
                    <!--      <li class="nav-item dropdown">
                        <a class="nav-link active dropdown-toggle bg-light" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            TÀI KHOẢN
                        </a>

                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="/dashboard">Thông tin tài khoản</a>
                            <a class="dropdown-item" href="/login">Đăng nhập</a>
                            <a class="dropdown-item" href="/register">Đăng ký</a>
                        </div>
           </li>  -->
               </ul> </div>
                    
           

            <!-- Form tìm kiếm phải -->
            <form class="d-flex ms-auto">
                <input class="form-control me-2" type="search" placeholder="Tìm kiếm sản phẩm..." aria-label="Search">
                <button class="btn btn-outline-primary" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </form>
<!-- Góc phải navbar -->
<div class="d-flex align-items-center ms-auto">

  

    <!-- Kiểm tra đăng nhập -->
    @if(Auth::check())
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                @if(Auth::user()->img == "")
                    <img src="{{ asset('storage/profile_images/default.jpg') }}"
                        alt="Default Profile Image"
                        class="rounded-circle"
                        style="width: 40px; height: 40px; object-fit: cover;">
                @else
                    <img src="{{ asset('storage/' . Auth::user()->img) }}"
                        alt="Profile Image"
                        class="rounded-circle"
                        style="width: 40px; height: 40px; object-fit: cover;">
                @endif
                <span class="ms-2 fw-semibold text-dark">{{ Auth::user()->name }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
    <li><a class="dropdown-item" href="/dashboard">Tài khoản của tôi</a></li>
    <li>
        <a href="{{ route('buyer.carts.index') }}" class="dropdown-item">
            <i class="fas fa-shopping-cart me-2"></i>Giỏ hàng {{ $totalItems }}
        </a>
    </li>
    
    <!-- Mục thông báo -->
    <li>
        <a class="dropdown-item" href="{{ route('notifications.index') }}">
            <i class="fas fa-bell me-2"></i>Thông báo
            @php
                $unreadCount = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count();
            @endphp
            @if($unreadCount > 0)
                <span class="badge bg-danger ms-2">{{ $unreadCount }}</span>
            @endif
        </a>
    </li>

    <li><hr class="dropdown-divider"></li>
    <li>
        <form action="/logout" method="POST" class="m-0">
            @csrf
            <button class="dropdown-item text-danger" type="submit">Đăng xuất</button>
        </form>
    </li>
</ul>
        </div>
    @else
      <a href="/login" 
   class="nav-link d-flex align-items-center px-3 py-2 rounded" 
   style="color: black; font-weight: 500; transition: 0.3s;">
   <i class="bi bi-person-circle me-2" style="font-size: 1.1rem;"></i>
     ĐĂNG NHẬP  
</a>


    @endif
</div>


        </div>
        </div>
    </nav>
    <!-- Bootstrap JS Bundle (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
