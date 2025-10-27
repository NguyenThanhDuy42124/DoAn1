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

                    <div class="dropdown-menu mega-menu shadow-lg border-0">
                        <div class="row g-0">

                            {{-- =================================== --}}
                            {{-- CỘT 1: DANH MỤC (TỰ ĐỘNG) --}}
                            {{-- =================================== --}}
                            <div class="col-lg-3 col-md-4 mega-menu-column-1">
                                <ul class="list-unstyled mb-0">

                                    {{-- Lặp qua các danh mục cha mà AppServiceProvider đã lấy --}}
                                    @foreach ($navbar_categories as $index => $category)
                                        <li class="mega-menu-item {{ $index == 0 ? 'active' : '' }}"
                                            data-target="#menu-cat-{{ $category->id }}">
                                            <a class="d-block p-3"
                                                href="{{ route('products.list', ['category' => $category->id]) }}">
                                                <i class="bi bi-tag me-2"></i> {{ $category->name }}
                                            </a>
                                        </li>
                                    @endforeach

                                </ul>
                            </div>

                            {{-- =================================== --}}
                            {{-- CỘT 2: HÃNG (BÁN TỰ ĐỘNG) --}}
                            {{-- =================================== --}}
                            <div class="col-lg-9 col-md-8 mega-menu-column-2 p-4">

                                {{-- Lặp qua các danh mục cha một lần nữa để TẠO RA các panel --}}
                                @foreach ($navbar_categories as $index => $category)
                                    <div class="mega-menu-content {{ $index == 0 ? 'active' : '' }}"
                                        id="menu-cat-{{ $category->id }}"
                                        style="{{ $index > 0 ? 'display: none;' : '' }}">

                                        <h5 class="mb-3">Thương hiệu</h5>

                                        <div class="row row-cols-2 row-cols-md-3 g-3">

                                            @php
                                                $brands_to_show = [];

                                                // === SỬA LỖI: Thêm \Illuminate\Support\Str:: ===
                                                if (\Illuminate\Support\Str::contains($category->name, 'Điện thoại')) {
                                                    $brands_to_show = [
                                                        'Apple',
                                                        'Samsung',
                                                        'Xiaomi',
                                                        'Oppo',
                                                        'Realme',
                                                        'Huawei',
                                                    ];
                                                } elseif (
                                                    \Illuminate\Support\Str::contains($category->name, 'Laptop')
                                                ) {
                                                    $brands_to_show = ['Apple', 'Dell', 'HP', 'Acer', 'Lenovo', 'Asus'];
                                                } elseif (
                                                    \Illuminate\Support\Str::contains($category->name, 'Tablet')
                                                ) {
                                                    $brands_to_show = ['Apple', 'Samsung', 'Xiaomi'];
                                                }
                                                // ===============================================
                                            @endphp

                                            {{-- Lọc và lặp qua các hãng đã định nghĩa --}}
                                            @foreach ($navbar_brands->whereIn('name', $brands_to_show) as $brand)
                                                <div class="col">
                                                    <a href="{{ route('products.list', ['category' => $category->id, 'brand' => $brand->id]) }}"
                                                        class="mega-brand-link">
                                                        {{ $brand->name }}
                                                    </a>
                                                </div>
                                            @endforeach

                                        </div>
                                    </div>
                                @endforeach

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
                        <a href="#" class="nav-link d-flex align-items-center dropdown-toggle" id="userDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            @if (Auth::user()->img == '')
                                <img src="{{ asset('storage/profile_images/default.jpg') }}" alt="Default Profile Image"
                                    class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
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
