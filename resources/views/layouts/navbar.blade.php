<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<nav class="navbar navbar-expand-lg navbar-dark fixed-top" aria-label="Eleventh navbar example">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="/">
            <span>DKD</span><span>SHOP</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample09"
            aria-controls="navbarsExample09" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarsExample09">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item dropdown">
                    {{-- Nút bấm "DANH MỤC" --}}
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-list me-1"></i> DANH MỤC
                    </a>

                    {{-- Nội dung Mega Menu --}}
                    <div class="dropdown-menu mega-menu shadow-lg border-0">
                        <div class="row g-0">

                            {{-- CỘT 1: DANH SÁCH DANH MỤC CHÍNH (L1) --}}
                            <div class="col-lg-3 col-md-4 mega-menu-column-1">
                                <ul class="list-unstyled mb-0">
                                    {{-- Mục 1: Điện thoại (Active mặc định) --}}
                                    <li class="mega-menu-item active" data-target="#menu-dienthoai">
                                        <a class="d-block p-3" href="#">
                                            <i class="bi bi-phone me-2"></i> Điện thoại, Tablet
                                        </a>
                                    </li>
                                    {{-- Mục 2: Laptop --}}
                                    <li class="mega-menu-item" data-target="#menu-laptop">
                                        <a class="d-block p-3" href="#">
                                            <i class="bi bi-laptop me-2"></i> Laptop
                                        </a>
                                    </li>
                                    {{-- Mục 3: Âm thanh --}}
                                    <li class="mega-menu-item" data-target="#menu-amthanh">
                                        <a class="d-block p-3" href="#">
                                            <i class="bi bi-headphones me-2"></i> Âm thanh, Mic
                                        </a>
                                    </li>
                                    {{-- Mục 4: Đồng hồ --}}
                                    <li class="mega-menu-item" data-target="#menu-dongho">
                                        <a class="d-block p-3" href="#">
                                            <i class="bi bi-smartwatch me-2"></i> Đồng hồ, Camera
                                        </a>
                                    </li>
                                    {{-- Mục 5: Phụ kiện --}}
                                    <li class="mega-menu-item" data-target="#menu-phukien">
                                        <a class="d-block p-3" href="#">
                                            <i class="bi bi-earbuds me-2"></i> Phụ kiện
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            {{-- CỘT 2: NỘI DUNG CHI TIẾT (L2) --}}
                            <div class="col-lg-9 col-md-8 mega-menu-column-2 p-4">

                                {{-- Panel 1: Điện thoại (Hiển thị mặc định) --}}
                                <div class="mega-menu-content active" id="menu-dienthoai">
                                    <h5 class="mb-3">Điện thoại, Tablet</h5>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <h6>Hãng điện thoại</h6>
                                            <ul class="list-unstyled">
                                                <li><a href="#" class="text-decoration-none">Apple (iPhone)</a>
                                                </li>
                                                <li><a href="#" class="text-decoration-none">Samsung</a></li>
                                                <li><a href="#" class="text-decoration-none">Xiaomi</a></li>
                                                <li><a href="#" class="text-decoration-none">OPPO</a></li>
                                            </ul>
                                        </div>
                                        <div class="col-md-4">
                                            <h6>Mức giá</h6>
                                            <ul class="list-unstyled">
                                                <li><a href="#" class="text-decoration-none">Dưới 2 triệu</a></li>
                                                <li><a href="#" class="text-decoration-none">Từ 2 - 4 triệu</a>
                                                </li>
                                                <li><a href="#" class="text-decoration-none">Từ 4 - 7 triệu</a>
                                                </li>
                                                <li><a href="#" class="text-decoration-none">Trên 10 triệu</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-md-4">
                                            <h6>Máy tính bảng</h6>
                                            <ul class="list-unstyled">
                                                <li><a href="#" class="text-decoration-none">iPad</a></li>
                                                <li><a href="#" class="text-decoration-none">Samsung Galaxy
                                                        Tab</a></li>
                                                <li><a href="#" class="text-decoration-none">Xiaomi Pad</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                {{-- Panel 2: Laptop (Ẩn) --}}
                                <div class="mega-menu-content" id="menu-laptop" style="display: none;">
                                    <h5 class="mb-3">Laptop</h5>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <h6>Hãng Laptop</h6>
                                            <ul class="list-unstyled">
                                                <li><a href="#" class="text-decoration-none">MacBook</a></li>
                                                <li><a href="#" class="text-decoration-none">Dell</a></li>
                                                <li><a href="#" class="text-decoration-none">HP</a></li>
                                                <li><a href="#" class="text-decoration-none">Lenovo</a></li>
                                            </ul>
                                        </div>
                                        <div class="col-md-4">
                                            <h6>Nhu cầu</h6>
                                            <ul class="list-unstyled">
                                                <li><a href="#" class="text-decoration-none">Văn phòng</a></li>
                                                <li><a href="#" class="text-decoration-none">Đồ họa, Kỹ
                                                        thuật</a></li>
                                                <li><a href="#" class="text-decoration-none">Gaming</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                {{-- Panel 3: Âm thanh (Ẩn) --}}
                                <div class="mega-menu-content" id="menu-amthanh" style="display: none;">
                                    <h5 class="mb-3">Âm thanh, Mic</h5>
                                    <p>Nội dung cho âm thanh...</p>
                                </div>

                                {{-- Panel 4: Đồng hồ (Ẩn) --}}
                                <div class="mega-menu-content" id="menu-dongho" style="display: none;">
                                    <h5 class="mb-3">Đồng hồ, Camera</h5>
                                    <p>Nội dung cho đồng hồ...</p>
                                </div>

                                {{-- Panel 5: Phụ kiện (Ẩn) --}}
                                <div class="mega-menu-content" id="menu-phukien" style="display: none;">
                                    <h5 class="mb-3">Phụ kiện</h5>
                                    <p>Nội dung cho phụ kiện...</p>
                                </div>

                            </div>
                        </div>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/products">DANH SÁCH SẢN PHẨM</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/vouchers">KHUYẾN MÃI</a>
                </li>
                <!--  <li class="nav-item">
                    <a class="nav-link" href="#">HỖ TRỢ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">GIỚI THIỆU</a>
                </li> -->


            </ul>

            <div class="d-flex mx-auto align-items-center">
                <form class="d-flex" role="search">
                    <input class="form-control me-2" type="search" placeholder="Tìm kiếm sản phẩm..."
                        aria-label="Search">
                    <button class="btn btn-outline-light" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
                <a href="#" class="location-display d-flex align-items-center text-decoration-none me-3">
                    <i class="fas fa-map-marker-alt me-2"></i>

                    <span class="location-text fw-semibold" id="user-location-text">
                        Đang tải vị trí... </span>

                    <i class="fas fa-chevron-right ms-2 location-chevron"></i>
                </a>
            </div>

            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                @if (Auth::check())
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link d-flex align-items-center dropdown-toggle"
                            id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            @if (Auth::user()->img == '')
                                <img src="{{ asset('storage/profile_images/default.jpg') }}"
                                    alt="Default Profile Image" class="rounded-circle"
                                    style="width: 40px; height: 40px; object-fit: cover;">
                            @else
                                <img src="{{ asset('storage/' . Auth::user()->img) }}" alt="Profile Image"
                                    class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                            @endif
                            <span class="ms-2 fw-semibold">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            {{-- 1. TÀI KHOẢN --}}
                            <li>
                                <a class="dropdown-item fw-bold" href="/dashboard">
                                    <i class="bi bi-person-circle me-2"></i> Tài khoản của tôi
                                </a>
                            </li>

                            {{-- 2. KÊNH NGƯỜI BÁN (Chỉ seller thấy) --}}
                            @if (Auth::user()->role == 'seller')
                                <li>
                                    <a class="dropdown-item fw-bold text-primary"
                                        href="{{ route('switchRole', 'seller') }}">
                                        <i class="bi bi-shop-window me-2"></i> Kênh Người Bán
                                    </a>
                                </li>
                            @endif

                            {{-- 3. TRANG QUẢN TRỊ (Chỉ admin thấy) - MỤC MỚI --}}
                            @if (Auth::user()->role == 'admin')
                                <li>
                                    {{-- Bạn có thể đổi route('switchRole', 'admin') thành route('admin.dashboard') nếu có --}}
                                    <a class="dropdown-item fw-bold text-primary"
                                        href="{{ route('switchRole', 'admin') }}">
                                        <i class="bi bi-shield-lock me-2"></i> Trang Quản Trị
                                    </a>
                                </li>
                            @endif

                            {{-- 4. VẠCH NGĂN (Chỉ hiện khi có 1 trong 2 mục trên) --}}
                            @if (Auth::user()->role == 'seller' || Auth::user()->role == 'admin')
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                            @endif

                            {{-- 5. GIỎ HÀNG --}}
                            <li>
                                <a href="{{ route('buyer.carts.index') }}"
                                    class="dropdown-item d-flex justify-content-between align-items-center fw-bold">

                                    {{-- Nhóm icon và chữ --}}
                                    <div>
                                        <i class="bi bi-cart me-2"></i> Giỏ hàng
                                    </div>

                                    {{-- Huy hiệu số lượng (chỉ hiện khi > 0) --}}
                                    @if ($totalItems > 0)
                                        <span class="badge bg-primary rounded-pill">{{ $totalItems }}</span>
                                    @endif
                                </a>
                            </li>

                            {{-- 6. THÔNG BÁO --}}
                            <li>
                                <a class="dropdown-item d-flex justify-content-between align-items-center fw-bold"
                                    href="{{ route('notifications.index') }}">

                                    {{-- Nhóm icon và chữ --}}
                                    <div>
                                        <i class="bi bi-bell me-2"></i> Thông báo
                                    </div>

                                    {{-- Mã PHP kiểm tra thông báo --}}
                                    @php
                                        $unreadCount = \App\Models\Notification::where('user_id', auth()->id())
                                            ->where('is_read', false)
                                            ->count();
                                    @endphp

                                    {{-- Huy hiệu số lượng (chỉ hiện khi > 0) --}}
                                    @if ($unreadCount > 0)
                                        <span class="badge bg-danger rounded-pill">{{ $unreadCount }}</span>
                                    @endif
                                </a>
                            </li>

                            {{-- 7. VẠCH NGĂN --}}
                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            {{-- 8. ĐĂNG XUẤT --}}
                            <li>
                                <form action="/logout" method="POST" class="m-0">
                                    @csrf
                                    <button class="dropdown-item fw-bold text-danger" type="submit">
                                        <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="/login" class="nav-link d-flex align-items-center px-3 py-2 rounded">
                            <i class="bi bi-person-circle me-2" style="font-size: 1.1rem;"></i>
                            ĐĂNG NHẬP
                        </a>
                    </li>
                @endif
            </ul>

        </div>
    </div>
</nav>
<script data-livewire-eval="false">
    // Chờ cho toàn bộ nội dung trang được tải
    document.addEventListener("DOMContentLoaded", function() {

        // Tìm phần tử <span> để hiển thị vị trí
        const locationTextElement = document.getElementById('user-location-text');

        // Đặt vị trí mặc định
        const defaultLocation = "Vị trí của bạn...";

        if (!locationTextElement) {
            console.error("Không tìm thấy phần tử 'user-location-text'.");
            return;
        }

        locationTextElement.textContent = defaultLocation; // Hiển thị mặc định trước

        // Kiểm tra xem trình duyệt có hỗ trợ Geolocation không
        if ("geolocation" in navigator) {
            // Lấy vị trí hiện tại
            // Tham số thứ nhất là callback khi thành công
            // Tham số thứ hai là callback khi thất bại
            navigator.geolocation.getCurrentPosition(handleSuccess, handleError);
        } else {
            console.warn("Trình duyệt này không hỗ trợ Geolocation.");
            // Giữ nguyên vị trí mặc định
        }

        // 1. Hàm xử lý khi LẤY ĐƯỢC vị trí
        function handleSuccess(position) {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;

            // Dùng API miễn phí của BigDataCloud để đổi tọa độ sang tên (Reverse Geocoding)
            // Yêu cầu kết quả bằng tiếng Việt (localityLanguage=vi)
            const apiUrl =
                `https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${latitude}&longitude=${longitude}&localityLanguage=vi`;

            fetch(apiUrl)
                .then(response => response.json())
                .then(data => {
                    // Lấy tên Thành phố (city) hoặc tên Tỉnh (principalSubdivision)
                    const locationName = data.principalSubdivision || data.city;

                    if (locationName) {
                        locationTextElement.textContent = locationName;
                        // Lưu vị trí vào sessionStorage để trang edit.blade.php có thể dùng
                        sessionStorage.setItem('userDetectedLocation', locationName);
                    } else {
                        locationTextElement.textContent = defaultLocation;
                    }
                })
                .catch(error => {
                    console.error("Lỗi khi gọi API reverse geocoding:", error);
                    locationTextElement.textContent = defaultLocation;
                });
        }

        // 2. Hàm xử lý khi NGƯỜI DÙNG TỪ CHỐI
        function handleError(error) {
            console.warn(`Lỗi khi lấy vị trí: ${error.message}`);
            // Nếu lỗi (ví dụ: người dùng từ chối), giữ nguyên vị trí mặc định
            locationTextElement.textContent = defaultLocation;
        }
    });
</script>
