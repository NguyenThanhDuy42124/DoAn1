<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<nav class="navbar navbar-expand-lg navbar-dark fixed-top" aria-label="Eleventh navbar example">
    
    <div class="container-lg">
        <a class="navbar-brand fw-bold" href="/">
            <span>DDK</span><span>Market</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample09"
            aria-controls="navbarsExample09" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarsExample09">

            <form class="d-flex flex-grow-1 mx-lg-3 my-2 my-lg-0" role="search" action="{{ route('products.list') }}" method="GET">
                <input class="form-control" 
                       type="search" 
                       placeholder="Tìm kiếm sản phẩm..."
                       aria-label="Search" 
                       name="search" 
                       value="{{ request('search') }}"> {{-- Giữ lại từ khóa đã tìm --}}
                       
                <button class="btn btn-outline-light" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </form>

            <ul class="navbar-nav mb-2 mb-lg-0">

                <li class="nav-item dropdown">
                    {{-- Nút bấm "DANH MỤC" --}}
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-list me-1"></i> DANH MỤC
                    </a>
                    
                    {{-- === BẮT ĐẦU CẤU TRÚC MEGA MENU MỚI === --}}
                    <div class="dropdown-menu mega-menu">
                        
                        <div class="mega-menu-inner-container">

                            {{-- CỘT 1 (NỘI DUNG GIỮ NGUYÊN) --}}
                            <div class="col-lg-3 col-md-4 mega-menu-column-1">
                                <ul class="list-unstyled mb-0">
                                    
                                    {{-- Lặp qua các danh mục cha ($navbar_categories) --}}
                                    @foreach($navbar_categories as $index => $category)
                                        <li class="mega-menu-item {{ $index == 0 ? 'active' : '' }}" 
                                            data-target="#menu-cat-{{ $category->id }}">
                                            <a class="d-block p-3" href="{{ route('products.list', ['category' => $category->id]) }}">
                                                <i class="bi bi-tag me-2"></i> {{ $category->name }}
                                            </a>
                                        </li>
                                    @endforeach

                                </ul>
                            </div>

                            {{-- CỘT 2 (NỘI DUNG GIỮ NGUYÊN) --}}
                            <div class="col-lg-9 col-md-8 mega-menu-column-2 p-4">

                                {{-- Lặp qua các danh mục cha một lần nữa để TẠO RA các panel --}}
                                @foreach($navbar_categories as $index => $category)
                                    <div class="mega-menu-content {{ $index == 0 ? 'active' : '' }}" 
                                         id="menu-cat-{{ $category->id }}">
                                        
                                        {{-- Tiêu đề động --}}
                                        <h5 class="mb-3">Thương hiệu {{ $category->name }}</h5>
                                        
                                        {{-- DANH SÁCH THƯƠNG HIỆU --}}
                                        <div class="row row-cols-2 row-cols-md-3 g-3 mega-brand-list">
                                            
                                            {{-- Lặp qua TOÀN BỘ $navbar_brands --}}
                                            @foreach ($navbar_brands as $brand)
                                                <div class="col">
                                                    <a href="{{ route('products.list', ['category' => $category->id, 'brand' => $brand->id]) }}" 
                                                       class="mega-brand-link"
                                                       data-base-href="{{ route('products.list', ['category' => $category->id, 'brand' => $brand->id]) }}">
                                                        {{ $brand->name }}
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>

                                        {{-- === PHẦN LỌC GIÁ (GIỮ NGUYÊN) === --}}
                                        <hr class="my-4">
                                        <div class="mega-price-filter">
                                            <label for="mega-price-{{ $category->id }}" class="form-label fw-semibold mb-2" style="font-size: 0.9rem;">
                                                <i class="fas fa-dollar-sign me-1"></i> Lọc theo mức giá
                                            </label>
                                            <select class="form-select form-select-sm mega-price-select" id="mega-price-{{ $category->id }}">
                                                <option value="">Tất cả mức giá</option>
                                                <option value="0-5000000">Dưới 5 triệu</option>
                                                <option value="5000000-10000000">5 - 10 triệu</option>
                                                <option value="10000000-20000000">10 - 20 triệu</option>
                                                <option value="20000000-">Trên 20 triệu</option>
                                            </select>
                                        </div>
                                        {{-- === KẾT THÚC PHẦN LỌC GIÁ === --}}

                                    </div>
                                @endforeach
                                
                            </div>

                        </div> </div>
                    {{-- === KẾT THÚC CẤU TRÚC MEGA MENU MỚI === --}}

                </li>
               
                <li class="nav-item">
                    <a class="nav-link" href="/vouchers">KHUYẾN MÃI</a>
                </li>
                
            </ul>

            <div class="d-flex align-items-center ms-auto">
                
                <a href="#" class="location-display d-flex align-items-center text-decoration-none me-3">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    <span class="location-text fw-semibold" id="user-location-text">
                        Đang tải vị trí... </span>
                    <i class="fas fa-chevron-right ms-2 location-chevron"></i>
                </a>

                <ul class="navbar-nav mb-2 mb-lg-0">
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

                                {{-- 3. TRANG QUẢN TRỊ (Chỉ admin thấy) --}}
                                @if (Auth::user()->role == 'admin')
                                    <li>
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
    </div>
</nav>

<script data-livewire-eval="false">
    document.addEventListener("DOMContentLoaded", function() {
        // (LOGIC MEGA MENU CŨ CỦA BẠN - GIỮ NGUYÊN)
        const menuItems = document.querySelectorAll('.mega-menu-item');
        const menuContents = document.querySelectorAll('.mega-menu-content');
        const priceSelects = document.querySelectorAll('.mega-price-select');
        menuItems.forEach(item => {
            item.addEventListener('mouseenter', function() {
                menuItems.forEach(i => i.classList.remove('active'));
                menuContents.forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                const targetId = this.getAttribute('data-target');
                const activeContent = document.querySelector(targetId);
                if (activeContent) {
                    activeContent.classList.add('active');
                    const currentSelect = activeContent.querySelector('.mega-price-select');
                    if (currentSelect) {
                        updateBrandLinks(activeContent, currentSelect.value);
                    }
                }
            });
        });
        priceSelects.forEach(select => {
            select.addEventListener('change', function() {
                const activeContent = this.closest('.mega-menu-content');
                if (activeContent) {
                    updateBrandLinks(activeContent, this.value);
                }
            });
        });
        function updateBrandLinks(contentPanel, priceRange) {
            const brandLinks = contentPanel.querySelectorAll('.mega-brand-link');
            brandLinks.forEach(link => {
                const baseHref = link.getAttribute('data-base-href');
                if (priceRange) {
                    const separator = baseHref.includes('?') ? '&' : '?';
                    link.setAttribute('href', `${baseHref}${separator}price_range=${priceRange}`);
                } else {
                    link.setAttribute('href', baseHref);
                }
            });
        }
        const initialActiveContent = document.querySelector('.mega-menu-content.active');
        if (initialActiveContent) {
             const initialSelect = initialActiveContent.querySelector('.mega-price-select');
             if (initialSelect) {
                updateBrandLinks(initialActiveContent, initialSelect.value);
             }
        }
        
        // (LOGIC GEOLOCATION CŨ CỦA BẠN - GIỮ NGUYÊN)
        const locationTextElement = document.getElementById('user-location-text');
        const defaultLocation = "Vị trí của bạn...";
        if (!locationTextElement) {
            console.error("Không tìm thấy phần tử 'user-location-text'.");
            return;
        }
        locationTextElement.textContent = defaultLocation;
        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(handleSuccess, handleError);
        } else {
            console.warn("Trình duyệt này không hỗ trợ Geolocation.");
        }
        function handleSuccess(position) {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;
            const apiUrl =
                `https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${latitude}&longitude=${longitude}&localityLanguage=vi`;
            fetch(apiUrl)
                .then(response => response.json())
                .then(data => {
                    const locationName = data.principalSubdivision || data.city;
                    if (locationName) {
                        locationTextElement.textContent = locationName;
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
        function handleError(error) {
            console.warn(`Lỗi khi lấy vị trí: ${error.message}`);
            locationTextElement.textContent = defaultLocation;
        }
        // 1. Tự động tạo 'div' lớp phủ và thêm vào <body>
        const overlay = document.createElement('div');
        overlay.className = 'page-overlay'; // Gán class CSS đã định nghĩa
        document.body.appendChild(overlay);

        // 2. Tìm 'li' cha chứa dropdown "DANH MỤC"
        const dropdownElement = document.querySelector('.nav-item.dropdown');

        if (dropdownElement) {
            // 3. Lắng nghe sự kiện "show" (khi menu BẮT ĐẦU mở)
            dropdownElement.addEventListener('show.bs.dropdown', function () {
                overlay.classList.add('show');
            });

            // 4. Lắng nghe sự kiện "hide" (khi menu BẮT ĐẦU đóng)
            dropdownElement.addEventListener('hide.bs.dropdown', function () {
                overlay.classList.remove('show');
            });
        }
    });
</script>