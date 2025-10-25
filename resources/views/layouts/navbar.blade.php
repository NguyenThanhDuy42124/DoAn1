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
                <li class="nav-item">
                    <a class="nav-link" href="/products">DANH SÁCH SẢN PHẨM</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/vouchers">KHUYẾN MÃI</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/ho-tro">HỖ TRỢ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/gioi-thieu">GIỚI THIỆU</a>
                </li>
                
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
                @if(Auth::check())
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link d-flex align-items-center dropdown-toggle" id="userDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            @if(Auth::user()->img == "")
                                <img src="{{ asset('storage/profile_images/default.jpg') }}" alt="Default Profile Image"
                                    class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                            @else
                                <img src="{{ asset('storage/' . Auth::user()->img) }}" alt="Profile Image"
                                    class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                            @endif
                            <span class="ms-2 fw-semibold">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="/dashboard">Tài khoản của tôi</a></li>
                            <li>
                                <a href="{{ route('buyer.carts.index') }}" class="dropdown-item">
                                    <i class="fas fa-shopping-cart me-2"></i>Giỏ hàng {{ $totalItems }}
                                </a>
                            </li>
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

        </div> </div>
</nav>
<script>
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
            const apiUrl = `https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${latitude}&longitude=${longitude}&localityLanguage=vi`;

            fetch(apiUrl)
                .then(response => response.json())
                .then(data => {
                    // Lấy tên Thành phố (city) hoặc tên Tỉnh (principalSubdivision)
                    const locationName = data.city || data.principalSubdivision;
                    
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>