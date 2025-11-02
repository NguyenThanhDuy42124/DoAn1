<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/css/layouts.css'])
    @vite(['resources/js/app.js'])
    <title>@yield('title', 'Trang chủ cửa hàng')</title>
    @stack('styles')
    @livewireStyles
</head>

<body class="d-flex flex-column min-vh-100">
 
    @include('layouts.navbar')


    <main class="flex-fill mt-0 pt-4">
        @if (isset($slot))
        {{ $slot }}
        @else
            @yield('content')
        @endif
    </main>

  
<footer class="py-5 mt-auto border-top">
    <div class="container">
        <div class="row">

            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <h6 class="text-uppercase fw-bold mb-4">Hỗ Trợ Khách Hàng</h6>
                <ul class="list-unstyled mb-3">
                    <li class="mb-2">
                        <span class="fw-semibold">Hotline:</span> 
                        <a href="tel:18000001" class="text-muted text-decoration-none">1800.0001</a>
                    </li>
                    <li class="mb-2">
                        <span class="fw-semibold">Email:</span> 
                        <a href="mailto:truongduy112098@gmail.com" class="text-muted text-decoration-none">hotro@trduy.dkdshop.com</a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="text-muted text-decoration-none">Câu hỏi thường gặp</a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="text-muted text-decoration-none">Tra cứu đơn hàng</a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="text-muted text-decoration-none">Chính sách bảo hành</a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="text-muted text-decoration-none">Chính sách đổi trả</a>
                    </li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <h6 class="text-uppercase fw-bold mb-4">Kênh Người Bán</h6>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <a href="#" class="text-muted text-decoration-none">Đăng ký bán hàng</a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="text-muted text-decoration-none">Đăng nhập Kênh Bán</a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="text-muted text-decoration-none">Hỗ trợ Người Bán</a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="text-muted text-decoration-none">Quy chế hoạt động Sàn</a>
                    </li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <h6 class="text-uppercase fw-bold mb-4">Thông tin & Chính sách</h6>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        {{-- Link này lấy từ navbar của bạn --}}
                        <a href="#" class="text-muted text-decoration-none">Giới thiệu DKDSHOP</a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="text-muted text-decoration-none">Tuyển dụng</a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="text-muted text-decoration-none">Chính sách bảo mật</a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="text-muted text-decoration-none">Điều khoản sử dụng</a>
                    </li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <h6 class="text-uppercase fw-bold mb-4">Kết nối với chúng tôi</h6>
                
                {{-- Yêu cầu Font Awesome (đã có trong file của bạn) --}}
                <div class="d-flex mb-4">
                    <a href="#" class="text-muted me-3" aria-label="Facebook">
                        <i class="fab fa-facebook-f fa-lg"></i>
                    </a>
                    <a href="#" class="text-muted me-3" aria-label="Instagram">
                        <i class="fab fa-instagram fa-lg"></i>
                    </a>
                    <a href="#" class="text-muted me-3" aria-label="Youtube">
                        <i class="fab fa-youtube fa-lg"></i>
                    </a>
                    <a href="#" class="text-muted" aria-label="Tiktok">
                        <i class="fab fa-tiktok fa-lg"></i>
                    </a>
                </div>
                
                <h6 class="text-uppercase fw-bold mb-4">Phương thức thanh toán</h6>
                <div class="d-flex flex-wrap" style="font-size: 2rem;">
                    <i class="fab fa-cc-visa me-2 mb-2 text-muted" title="Visa"></i>
                    <i class="fab fa-cc-mastercard me-2 mb-2 text-muted" title="Mastercard"></i>
                    <i class="fab fa-cc-jcb me-2 mb-2 text-muted" title="JCB"></i>
                    <i class="fab fa-paypal me-2 mb-2 text-muted" title="Paypal"></i>
                </div>
            </div>
            
        </div>

        <div class="text-center pt-4 mt-4 border-top">
            <p class="mb-1 text-muted">&copy; {{ date('Y') }} DKDSHOP. Đã đăng ký bản quyền.</p>
            <p class="mb-0 text-muted small">
                Công ty TNHH DKDSHOP | Địa chỉ: 255 Đường Nguyễn Văn Cừ, Phường Cái Khế, TP. Cần Thơ
            </p>
        </div>
    </div>
</footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" data-livewire-eval="false"></script>
 @livewireScripts
 @if(config('chatify.pusher.key') != null)
    <button
    type="button"
    id="chat-bubble"
    class="btn btn-primary shadow rounded-circle"
    onclick="window.location.href='{{ route('chatify') }}'">
    {{-- Nút chat đã dùng icon 'bi' từ navbar, ta giữ nguyên --}}
    <i class="bi bi-chat-dots-fill"></i> 
    </button>
 @endif
 <script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Xử lý hover cho Mega Menu
    const megaMenuItems = document.querySelectorAll('.mega-menu-item');
    const megaMenuContents = document.querySelectorAll('.mega-menu-content');

    megaMenuItems.forEach(item => {
        // Dùng 'mouseenter' (rê chuột vào)
        item.addEventListener('mouseenter', function() {
            
            // 1. Xóa 'active' khỏi TẤT CẢ các mục L1
            megaMenuItems.forEach(i => i.classList.remove('active'));
            
            // 2. Thêm 'active' cho mục L1 đang được hover
            this.classList.add('active');
            
            // 3. Lấy ID của nội dung L2 cần hiển thị (từ 'data-target')
            const targetId = this.getAttribute('data-target');
            
            // 4. Ẩn TẤT CẢ nội dung L2
            megaMenuContents.forEach(content => {
                content.style.display = 'none';
                content.classList.remove('active');
            });
            
            // 5. Hiển thị nội dung L2 tương ứng
            const targetContent = document.querySelector(targetId);
            if (targetContent) {
                targetContent.style.display = 'block';
                targetContent.classList.add('active');
            }
        });
    });

    // (Tùy chọn) Ngăn dropdown tự đóng khi click bên trong menu
    document.querySelector('.mega-menu').addEventListener('click', function(e) {
        e.stopPropagation();
    });
});
</script>
</body>

</html>
