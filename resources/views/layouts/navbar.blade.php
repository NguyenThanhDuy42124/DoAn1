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

            <form class="d-flex flex-grow-1 mx-lg-3 my-2 my-lg-0" role="search" action="{{ route('products.list') }}"
                method="GET">
                <input class="form-control" type="search" placeholder="Tìm kiếm sản phẩm..." aria-label="Search"
                    name="search" value="{{ request('search') }}"> {{-- Giữ lại từ khóa đã tìm --}}

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

                            {{-- CỘT 2 (NỘI DUNG GIỮ NGUYÊN) --}}
                            <div class="col-lg-9 col-md-8 mega-menu-column-2 p-4">

                                {{-- Lặp qua các danh mục cha một lần nữa để TẠO RA các panel --}}
                                @foreach ($navbar_categories as $index => $category)
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
                                            <label for="mega-price-{{ $category->id }}"
                                                class="form-label fw-semibold mb-2" style="font-size: 0.9rem;">
                                                <i class="fas fa-dollar-sign me-1"></i> Lọc theo mức giá
                                            </label>
                                            <select class="form-select form-select-sm mega-price-select"
                                                id="mega-price-{{ $category->id }}">
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

                        </div>
                    </div>
                    {{-- === KẾT THÚC CẤU TRÚC MEGA MENU MỚI === --}}

                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/vouchers">KHUYẾN MÃI</a>
                </li>

            </ul>

            <div class="d-flex align-items-center ms-auto">

                {{-- (Nút này giữ nguyên) --}}
                <a href="#" class="location-display d-flex align-items-center text-decoration-none me-3"
                    data-bs-toggle="modal" data-bs-target="#locationModal">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    <span class="location-text fw-semibold" id="user-location-text">
                        Chọn vị trí...
                    </span>
                    <i class="fas fa-chevron-right ms-2 location-chevron"></i>
                </a>

                <ul class="navbar-nav mb-2 mb-lg-0">
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


<div class="modal fade" id="locationModal" tabindex="-1" aria-labelledby="locationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg"> {{-- Sử dụng modal-lg cho kích thước lớn --}}
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="locationModalLabel">Chọn tỉnh, thành phố của bạn</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Thanh tìm kiếm --}}
                <div class="mb-3">
                    <input type="search" class="form-control" id="location-search-input"
                        placeholder="Tìm kiếm tỉnh, thành phố...">
                </div>

                {{-- *** THAY ĐỔI 2: CẬP NHẬT HTML *** --}}
                {{-- Danh sách tỉnh thành (dạng lưới 2 cột) --}}
                <div class="row row-cols-2 row-cols-md-3 g-2" id="location-list-container">

                    {{-- 
                      Mỗi item <a> giờ đây chứa 1 <span> (cho văn bản) và 1 <i> (cho icon)
                      Bạn cần áp dụng cấu trúc này cho TẤT CẢ 63 tỉnh thành của bạn.
                    --}}
                    
                    <div>
                        <a href="#" class="location-select-item" data-location="An Giang">
                            <span>An Giang</span>
                            <i class="bi bi-check-lg location-check-icon"></i>
                        </a>
                    </div>
                    <div>
                        <a href="#" class="location-select-item" data-location="Bắc Ninh">
                            <span>Bắc Ninh</span>
                            <i class="bi bi-check-lg location-check-icon"></i>
                        </a>
                    </div>
                    <div>
                        <a href="#" class="location-select-item" data-location="Bạc Liêu">
                            <span>Bạc Liêu</span>
                            <i class="bi bi-check-lg location-check-icon"></i>
                        </a>
                    </div>
                    <div>
                        <a href="#" class="location-select-item" data-location="Cà Mau">
                            <span>Cà Mau</span>
                            <i class="bi bi-check-lg location-check-icon"></i>
                        </a>
                    </div>
                    <div>
                        <a href="#" class="location-select-item" data-location="Cao Bằng">
                            <span>Cao Bằng</span>
                            <i class="bi bi-check-lg location-check-icon"></i>
                        </a>
                    </div>
                    <div>
                        <a href="#" class="location-select-item" data-location="Đắk Lắk">
                            <span>Đắk Lắk</span>
                            <i class="bi bi-check-lg location-check-icon"></i>
                        </a>
                    </div>
                    <div>
                        <a href="#" class="location-select-item" data-location="Điện Biên">
                            <span>Điện Biên</span>
                            <i class="bi bi-check-lg location-check-icon"></i>
                        </a>
                    </div>
                    <div>
                        <a href="#" class="location-select-item" data-location="Đồng Nai">
                            <span>Đồng Nai</span>
                            <i class="bi bi-check-lg location-check-icon"></i>
                        </a>
                    </div>
                    <div>
                        <a href="#" class="location-select-item" data-location="Đồng Tháp">
                            <span>Đồng Tháp</span>
                            <i class="bi bi-check-lg location-check-icon"></i>
                        </a>
                    </div>
                    <div>
                        <a href="#" class="location-select-item" data-location="Gia Lai">
                            <span>Gia Lai</span>
                            <i class="bi bi-check-lg location-check-icon"></i>
                        </a>
                    </div>
                    <div>
                        <a href="#" class="location-select-item" data-location="Hà Tĩnh">
                            <span>Hà Tĩnh</span>
                            <i class="bi bi-check-lg location-check-icon"></i>
                        </a>
                    </div>
                    <div>
                        <a href="#" class="location-select-item" data-location="Hưng Yên">
                            <span>Hưng Yên</span>
                            <i class="bi bi-check-lg location-check-icon"></i>
                        </a>
                    </div>
                    <div>
                        <a href="#" class="location-select-item" data-location="Khánh Hòa">
                            <span>Khánh Hòa</span>
                            <i class="bi bi-check-lg location-check-icon"></i>
                        </a>
                    </div>
                    <div>
                        <a href="#" class="location-select-item" data-location="Lai Châu">
                            <span>Lai Châu</span>
                            <i class="bi bi-check-lg location-check-icon"></i>
                        </a>
                    </div>
                    <div>
                        <a href="#" class="location-select-item" data-location="Lâm Đồng">
                            <span>Lâm Đồng</span>
                            <i class="bi bi-check-lg location-check-icon"></i>
                        </a>
                    </div>
                    <div>
                        <a href="#" class="location-select-item" data-location="Lạng Sơn">
                            <span>Lạng Sơn</span>
                            <i class="bi bi-check-lg location-check-icon"></i>
                        </a>
                    </div>
                    <div>
                        <a href="#" class="location-select-item" data-location="Lào Cai">
                            <span>Lào Cai</span>
                            <i class="bi bi-check-lg location-check-icon"></i>
                        </a>
                    </div>
                    <div>
                        <a href="#" class="location-select-item" data-location="Nghệ An">
                            <span>Nghệ An</span>
                            <i class="bi bi-check-lg location-check-icon"></i>
                        </a>
                    </div>
                    <div>
                        <a href="#" class="location-select-item" data-location="Ninh Bình">
                            <span>Ninh Bình</span>
                            <i class="bi bi-check-lg location-check-icon"></i>
                        </a>
                    </div>
                    <div>
                        <a href="#" class="location-select-item" data-location="Phú Thọ">
                            <span>Phú Thọ</span>
                            <i class="bi bi-check-lg location-check-icon"></i>
                        </a>
                    </div>
                    <div>
                        <a href="#" class="location-select-item" data-location="Quảng Ngãi">
                            <span>Quảng Ngãi</span>
                            <i class="bi bi-check-lg location-check-icon"></i>
                        </a>
                    </div>
                    <div>
                        <a href="#" class="location-select-item" data-location="Quảng Ninh">
                            <span>Quảng Ninh</span>
                            <i class="bi bi-check-lg location-check-icon"></i>
                        </a>
                    </div>
                    <div>
                        <a href="#" class="location-select-item" data-location="Quảng Trị">
                            <span>Quảng Trị</span>
                            <i class="bi bi-check-lg location-check-icon"></i>
                        </a>
                    </div>
                    <div>
                        <a href="#" class="location-select-item" data-location="Sơn La">
                            <span>Sơn La</span>
                            <i class="bi bi-check-lg location-check-icon"></i>
                        </a>
                    </div>
                    <div>
                        <a href="#" class="location-select-item" data-location="Tây Ninh">
                            <span>Tây Ninh</span>
                            <i class="bi bi-check-lg location-check-icon"></i>
                        </a>
                    </div>
                    <div>
                        <a href="#" class="location-select-item" data-location="Thái Nguyên">
                            <span>Thái Nguyên</span>
                            <i class="bi bi-check-lg location-check-icon"></i>
                        </a>
                    </div>
                    <div>
                        <a href="#" class="location-select-item" data-location="Thanh Hóa">
                            <span>Thanh Hóa</span>
                            <i class="bi bi-check-lg location-check-icon"></i>
                        </a>
                    </div>
                    <div>
                        <a href="#" class="location-select-item" data-location="Trà Vinh">
                            <span>Trà Vinh</span>
                            <i class="bi bi-check-lg location-check-icon"></i>
                        </a>
                    </div>
                    <div>
                        <a href="#" class="location-select-item" data-location="TP. Cần Thơ">
                            <span>TP. Cần Thơ</span>
                            <i class="bi bi-check-lg location-check-icon"></i>
                        </a>
                    </div>
                    <div>
                        <a href="#" class="location-select-item" data-location="TP. Đà Nẵng">
                            <span>TP. Đà Nẵng</span>
                            <i class="bi bi-check-lg location-check-icon"></i>
                        </a>
                    </div>
                    <div>
                        <a href="#" class="location-select-item" data-location="TP. Hà Nội">
                            <span>TP. Hà Nội</span>
                            <i class="bi bi-check-lg location-check-icon"></i>
                        </a>
                    </div>
                    <div>
                        <a href="#" class="location-select-item" data-location="TP. Hải Phòng">
                            <span>TP. Hải Phòng</span>
                            <i class="bi bi-check-lg location-check-icon"></i>
                        </a>
                    </div>
                    <div>
                        <a href="#" class="location-select-item" data-location="TP. Hồ Chí Minh">
                            <span>TP. Hồ Chí Minh</span>
                            <i class="bi bi-check-lg location-check-icon"></i>
                        </a>
                    </div>
                    <div>
                        <a href="#" class="location-select-item" data-location="TP. Huế">
                            <span>TP. Huế</span>
                            <i class="bi bi-check-lg location-check-icon"></i>
                        </a>
                    </div>
                    <div>
                        <a href="#" class="location-select-item" data-location="Tuyên Quang">
                            <span>Tuyên Quang</span>
                            <i class="bi bi-check-lg location-check-icon"></i>
                        </a>
                    </div>
                    <div>
                        <a href="#" class="location-select-item" data-location="Vĩnh Long">
                            <span>Vĩnh Long</span>
                            <i class="bi bi-check-lg location-check-icon"></i>
                        </a>
                    </div>

                    {{-- ... Bạn nhớ cập nhật TẤT CẢ các tỉnh thành khác theo cấu trúc mới này nhé ... --}}

                </div>
            </div>
        </div>
    </div>
</div>

{{-- *** THAY ĐỔI 3: CẬP NHẬT JAVASCRIPT *** --}}
<script data-livewire-eval="false">
    document.addEventListener("DOMContentLoaded", function() {

        // (Logic Mega Menu & Overlay giữ nguyên)
        // =============================================
        // === LOGIC MEGA MENU (GIỮ NGUYÊN) ===
        // =============================================
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
        // =============================================
        // === LOGIC LỚP PHỦ (OVERLAY - GIỮ NGUYÊN) ===
        // =============================================
        const overlay = document.createElement('div');
        overlay.className = 'page-overlay';
        document.body.appendChild(overlay);
        const dropdownElement = document.querySelector('.nav-item.dropdown');
        if (dropdownElement) {
            dropdownElement.addEventListener('show.bs.dropdown', function() {
                overlay.classList.add('show');
            });
            dropdownElement.addEventListener('hide.bs.dropdown', function() {
                overlay.classList.remove('show');
            });
        }

        // =============================================
        // === LOGIC VỊ TRÍ (LOCATION) - ĐÃ CẬP NHẬT ===
        // =============================================

        const locationTextElement = document.getElementById('user-location-text');
        const locationModalEl = document.getElementById('locationModal');
        const locationModalInstance = new bootstrap.Modal(locationModalEl);
        const searchInput = document.getElementById('location-search-input');
        const listContainer = document.getElementById('location-list-container');
        const locationItems = listContainer.querySelectorAll('div'); // Các 'div' bọc 'a'

        const defaultLocationText = "Chọn vị trí...";
        const chosenLocationKey = 'userChosenLocation'; 
        const detectedLocationKey = 'userDetectedLocation'; 

        // 1. Tải vị trí khi trang được tải (giữ nguyên)
        loadLocation();

        // 2. Xử lý khi người dùng CHỌN một vị trí từ modal (ĐÃ CẬP NHẬT)
        listContainer.addEventListener('click', function(e) {
            
            // Tìm thẻ <a> cha gần nhất, đề phòng user click vào <span> hoặc <i>
            const clickedLink = e.target.closest('.location-select-item'); 

            if (clickedLink) {
                e.preventDefault();
                const selectedLocation = clickedLink.dataset.location; // Lấy từ 'data-location'

                // Cập nhật text trên navbar
                locationTextElement.textContent = selectedLocation;

                // Lưu vào localStorage để ghi nhớ
                localStorage.setItem(chosenLocationKey, selectedLocation);

                // Xóa vị trí tự động (nếu có) để ưu tiên vị trí đã chọn
                sessionStorage.removeItem(detectedLocationKey);

                // *** BẮT ĐẦU LOGIC ACTIVE CLASS ***
                // 1. Xóa active class khỏi tất cả các item
                listContainer.querySelectorAll('.location-select-item').forEach(link => {
                    link.classList.remove('active');
                });
                
                // 2. Thêm active class cho item được click
                clickedLink.classList.add('active');
                // *** KẾT THÚC LOGIC ACTIVE CLASS ***

                // Đóng modal
                locationModalInstance.hide();
            }
        });

        // 3. Xử lý TÌM KIẾM trong modal (ĐÃ CẬP NHẬT để lọc cả <span>)
        searchInput.addEventListener('input', function() {
            const filter = searchInput.value.toLowerCase().trim();
            
            locationItems.forEach(itemWrapper => {
                const link = itemWrapper.querySelector('.location-select-item');
                if (link) {
                    // Tìm văn bản bên trong thẻ <span> con
                    const textElement = link.querySelector('span');
                    if (textElement) {
                        const text = textElement.textContent.toLowerCase();
                        if (text.includes(filter)) {
                            itemWrapper.style.display = 'block';
                        } else {
                            itemWrapper.style.display = 'none';
                        }
                    }
                }
            });
        });

        // 4. Reset thanh tìm kiếm khi modal được mở (ĐÃ CẬP NHẬT)
        locationModalEl.addEventListener('show.bs.modal', function() {
            searchInput.value = ''; // Xóa nội dung tìm kiếm cũ
            
            // Reset lại danh sách (hiển thị tất cả)
            locationItems.forEach(itemWrapper => {
                itemWrapper.style.display = 'block';
            });

            // *** BẮT ĐẦU LOGIC CẬP NHẬT CHECKMARK ***
            // 1. Lấy vị trí đang được lưu (ưu tiên đã chọn)
            const currentSavedLocation = localStorage.getItem(chosenLocationKey) || sessionStorage.getItem(detectedLocationKey);

            // 2. Lặp qua tất cả các link và đặt active class
            listContainer.querySelectorAll('.location-select-item').forEach(link => {
                const linkLocation = link.dataset.location;
                if (linkLocation === currentSavedLocation) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });
            // *** KẾT THÚC LOGIC CẬP NHẬT CHECKMARK ***
        });

        // 5. Hàm tải vị trí (giữ nguyên)
        function loadLocation() {
            const chosenLocation = localStorage.getItem(chosenLocationKey);
            const detectedLocation = sessionStorage.getItem(detectedLocationKey);

            if (chosenLocation) {
                // Ưu tiên 1: Vị trí người dùng đã tự chọn
                locationTextElement.textContent = chosenLocation;
            } else if (detectedLocation) {
                // Ưu tiên 2: Vị trí được phát hiện (từ lần tải trước)
                locationTextElement.textContent = detectedLocation;
                runGeolocation();
            } else {
                // Ưu tiên 3: Chạy geolocation lần đầu
                locationTextElement.textContent = "Đang tải vị trí...";
                runGeolocation();
            }
        }

        // 6. Hàm chạy Geolocation (giữ nguyên)
        function runGeolocation() {
            if (localStorage.getItem(chosenLocationKey)) {
                return;
            }
            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(handleGeoSuccess, handleGeoError);
            } else {
                console.warn("Trình duyệt này không hỗ trợ Geolocation.");
                locationTextElement.textContent = defaultLocationText;
            }
        }

        // 7. Geolocation thành công (giữ nguyên)
        function handleGeoSuccess(position) {
            if (localStorage.getItem(chosenLocationKey)) {
                return;
            }
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;
            const apiUrl =
                `https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${latitude}&longitude=${longitude}&localityLanguage=vi`;

            fetch(apiUrl)
                .then(response => response.json())
                .then(data => {
                    const locationName = data.principalSubdivision || data.city;
                    if (locationName) {
                        if (!localStorage.getItem(chosenLocationKey)) {
                            locationTextElement.textContent = locationName;
                            sessionStorage.setItem(detectedLocationKey, locationName);
                        }
                    } else {
                        if (!localStorage.getItem(chosenLocationKey)) {
                            locationTextElement.textContent = defaultLocationText;
                        }
                    }
                })
                .catch(error => {
                    console.error("Lỗi khi gọi API reverse geocoding:", error);
                    if (!localStorage.getItem(chosenLocationKey)) {
                        locationTextElement.textContent = defaultLocationText;
                    }
                });
        }

        // 8. Geolocation thất bại (giữ nguyên)
        function handleGeoError(error) {
            console.warn(`Lỗi khi lấy vị trí: ${error.message}`);
            if (!localStorage.getItem(chosenLocationKey)) {
                locationTextElement.textContent = defaultLocationText;
            }
        }

    });
</script>