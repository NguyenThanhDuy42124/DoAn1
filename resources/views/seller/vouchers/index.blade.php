<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    @vite(['resources/css/vouchers.css', 'resources/js/app.js'])
    <title>Danh sách khuyến mãi</title>
</head>
<body>
    <div class="container">
    <h2 class="mb-4">Quản lý Voucher</h2>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="mb-3 d-flex">
    <a href="{{ route('vouchers.create') }}" class="btn btn-primary">
        <i class="fas fa-plus mr-2"></i>Thêm Voucher
    </a>
    <a href="{{ route('seller.dashboard') }}" class="btn btn-primary ms-auto">
       <i class="fas fa-home mr-2"></i> Quay lại
    </a>
</div>

    @if($vouchers->count())
        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Giảm giá</th>
                    <th>Điều kiện</th>
                    <th>Ngày hết hạn</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vouchers as $voucher)
                    <tr>
                        <td>{{ $voucher->id }}</td>
                        <td>{{ $voucher->discount_rate }}%</td>
                        <td>{{ $voucher->condition ?? 'Không có' }}</td>
                        <td>{{ $voucher->expiry_date->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('vouchers.edit', $voucher->id) }}" class="btn btn-sm btn-primary">
                                Sửa
                            </a>
                            <form action="{{ route('vouchers.destroy', $voucher->id) }}" 
                                  method="POST" class="d-inline"
                                  >
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    Xóa
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-3">
            {{ $vouchers->links() }}
        </div>
    @else
        <p>Hiện chưa có voucher nào.</p>
    @endif
</div>

</body>

