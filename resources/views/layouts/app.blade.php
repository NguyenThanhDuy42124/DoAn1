<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    @vite(['resources/css/layouts.css'])
    <title>@yield('title', 'Trang chủ cửa hàng')</title>
</head>

<body class="d-flex flex-column min-vh-100">
 
    @include('layouts.navbar')


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

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"
        integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"
        integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous">
    </script>
</body>

</html>
