@extends('layouts.app') {{-- Kế thừa layout chính của bạn --}}

@section('content')
<div class="container py-5">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('buyer.carts.index') }}">Giỏ hàng</a></li>
            <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Thanh toán</li>
        </ol>
    </nav>

    {{-- Thông báo lỗi/thành công --}}
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-4">
        {{-- CỘT TRÁI: THÔNG TIN ĐƠN HÀNG --}}
        <div class="col-lg-8">

            {{-- 1. Địa chỉ nhận hàng --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-primary"><i class="fas fa-map-marker-alt me-2"></i> Địa chỉ nhận hàng</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="fw-bold mb-1">{{ $user->name }} <span class="fw-normal text-muted">({{ $user->phoneNumber ?? 'Chưa có SĐT' }})</span></h6>
                            <p class="mb-0 text-secondary">{{ $shippingAddress }}</p>
                        </div>

                    </div>
                </div>
            </div>

            {{-- 2. Danh sách sản phẩm (Group by Seller) --}}
            @foreach($ordersBySeller as $sellerId => $group)
            <div class="card shadow-sm border-0 mb-4">
                {{-- Header Shop --}}
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-store text-secondary me-2"></i>
                        <span class="fw-bold">{{ $group['seller_name'] }}</span>
                    </div>
                </div>

                {{-- Body: List Item --}}
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <tbody>
                                @foreach($group['items'] as $item)
                                <tr>
                                    <td style="width: 70px;" class="ps-3 py-3">
                                        @php
                                        // Kiểm tra an toàn: Nếu có ảnh thì lấy ảnh đầu tiên, không thì lấy ảnh mặc định
                                        $imagePath = $item->product->images->isNotEmpty()
                                        ? asset('storage/' . $item->product->images->first()->image_path)
                                        : asset('storage/product_images/default.jpg');
                                        @endphp

                                        <img src="{{ $imagePath }}" class="rounded border" style="width: 60px; height: 60px; object-fit: cover;" alt="{{ $item->product->name }}">
                                    </td>
                                    <td class="py-3">
                                        <div class="fw-medium">{{ $item->product->name }}</div>
                                        <div class="text-muted small">x{{ $item->quantity }}</div>
                                    </td>
                                    <td class="text-end pe-3 py-3">
                                        {{ number_format($item->price * $item->quantity, 0, ',', '.') }}₫
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Footer: Voucher Shop & Tổng shop --}}
                <div class="card-footer bg-light">

                    {{-- Form nhập Voucher --}}
                    <div class="row align-items-center mb-3">
                        <div class="col-md-7">
                            @if($group['voucher'])
                            {{-- Hiển thị Voucher ĐÃ ÁP DỤNG --}}
                            <div class="alert alert-success d-flex justify-content-between align-items-center py-2 mb-0">
                                <div>
                                    <i class="fas fa-ticket-alt me-2"></i>
                                    Đã dùng: <strong>{{ $group['voucher']->code }}</strong>
                                    <small class="text-muted ms-1">(-{{ number_format($group['discount_amount']) }}₫)</small>
                                </div>

                                <form action="{{ route('buyer.checkouts.remove-voucher') }}" method="POST" class="m-0">
                                    @csrf
                                    <input type="hidden" name="seller_id" value="{{ $sellerId }}">
                                    <button type="submit" class="btn-close btn-sm" aria-label="Close"></button>
                                </form>
                            </div>
                            @else
                            {{-- Form nhập Voucher MỚI --}}
                            <form action="{{ route('buyer.checkouts.apply-voucher') }}" method="POST" class="d-flex">
                                @csrf
                                <input type="hidden" name="seller_id" value="{{ $sellerId }}">
                                <input type="text" name="code" class="form-control me-2" placeholder="Mã Shop Voucher (VD: SALE10)" required>
                                <button type="submit" class="btn btn-outline-primary">Áp dụng</button>
                            </form>
                            @if(isset($group['voucher_error']))
                            <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle"></i> {{ $group['voucher_error'] }}</div>
                            @endif
                            @if(!empty($group['available_vouchers']) && $group['voucher'] === null)
                            <div class="mt-2">
                                <div class="text-muted small mb-1">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Voucher khả dụng cho shop:
                                </div>

                                @foreach($group['available_vouchers'] as $v)
                                <form action="{{ route('buyer.checkouts.apply-voucher') }}" method="POST" class="d-flex align-items-center mb-2">
                                    @csrf
                                    <input type="hidden" name="seller_id" value="{{ $sellerId }}">
                                    <input type="hidden" name="code" value="{{ $v->code }}">

                                    <div class="p-2 border rounded w-100">
                                        <div class="fw-bold">{{ $v->code }}</div>

                                        <div class="small text-muted">
                                            @if($v->type === 'fixed')
                                            Giảm {{ number_format($v->value) }}₫
                                            @else
                                            Giảm {{ $v->value }}%
                                            @if($v->max_discount_amount)
                                            (tối đa {{ number_format($v->max_discount_amount) }}₫)
                                            @endif
                                            @endif
                                        </div>

                                        <div class="small text-muted">
                                            Đơn tối thiểu: {{ number_format($v->min_order_value) }}₫
                                        </div>
                                    </div>

                                    <button class="btn btn-primary btn-sm ms-2">Dùng</button>
                                </form>
                                @endforeach
                            </div>
                            @endif

                            @endif
                        </div>

                        <div class="col-md-5 text-end">
                            <div class="d-flex justify-content-between text-muted small">
                                <span>Tổng tiền hàng:</span>
                                <span>{{ number_format($group['subtotal'], 0, ',', '.') }}₫</span>
                            </div>
                            @if($group['discount_amount'] > 0)
                            <div class="d-flex justify-content-between text-success small">
                                <span>Voucher giảm:</span>
                                <span>-{{ number_format($group['discount_amount'], 0, ',', '.') }}₫</span>
                            </div>
                            @endif
                            <hr class="my-2">
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Thành tiền:</span>
                                <span class="text-danger fs-5">{{ number_format($group['final_total'], 0, ',', '.') }}₫</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- CỘT PHẢI: TỔNG THANH TOÁN --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Thông tin thanh toán</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tổng tiền hàng:</span>
                        <span class="fw-bold">{{ number_format(collect($ordersBySeller)->sum('subtotal'), 0, ',', '.') }}₫</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 text-success">
                        <span class="text-muted">Tổng giảm giá:</span>
                        <span>-{{ number_format(collect($ordersBySeller)->sum('discount_amount'), 0, ',', '.') }}₫</span>
                    </div>


                    <hr>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fw-bold fs-5">Tổng thanh toán:</span>
                        <span class="fw-bold fs-3 text-danger">{{ number_format($grandTotal, 0, ',', '.') }}₫</span>
                    </div>

                    {{-- FORM THANH TOÁN CUỐI CÙNG --}}
                    <form action="{{ route('buyer.checkouts.process') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100 py-3 fw-bold text-uppercase shadow-sm">
                            Xác nhận thanh toán
                        </button>
                    </form>
                    <div class="text-center mt-3">
                        <small class="text-muted"><i class="fas fa-shield-alt me-1"></i> Thanh toán an toàn & bảo mật</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
