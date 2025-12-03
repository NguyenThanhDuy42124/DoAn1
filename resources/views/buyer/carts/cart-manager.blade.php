<div>
    {{-- Thông báo --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Giỏ hàng của tôi</h5>
        </div>

        @if (empty($cartItems))
            {{-- Giao diện giỏ hàng trống --}}
            <div class="card-body text-center py-5">
                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Giỏ hàng của bạn đang trống.</h5>
                <p class="text-muted mb-0">Hãy quay lại và chọn cho mình sản phẩm ưng ý nhé!</p>
                <a href="{{ route('products.list') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-reply"></i> Tiếp tục mua hàng
                </a>
            </div>
        @else
            {{-- Bảng giỏ hàng --}}
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 5%;"></th>
                                <th colspan="2">Sản phẩm</th>
                                <th class="text-center" style="width: 15%;">Đơn giá</th>
                                <th class="text-center" style="width: 15%;">Số lượng</th>
                                <th class="text-center" style="width: 15%;">Thành tiền</th>
                                <th class="text-center" style="width: 10%;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($groupedItems as $sellerId => $items)
                                @php
                                    $firstItem = $items->first();
                                    
                                    // SỬA Ở ĐÂY:
                                    // 1. Đổi thành biến $shopName cho đồng bộ với bên dưới.
                                    // 2. Gọi ưu tiên cột 'shop_name'. Nếu shop_name null thì mới lấy 'name'.
                                    $shopName = $firstItem['product']['seller']['shop_name'] 
                                                ?? $firstItem['product']['seller']['name'] 
                                                ?? 'Shop #' . $sellerId;
                                @endphp

                                {{-- Header Shop --}}
                                <tr class="table-secondary">
                                    <td colspan="7" class="py-2 px-3">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-store me-2 text-primary"></i>
                                            <a href="{{ route('shop.show', ['id' => $sellerId]) }}" 
                                            class="text-decoration-none fw-bold text-dark">
                                                {{-- Bây giờ nó sẽ hiện đúng shop_name --}}
                                                {{ $shopName }}
                                            </a>
                                        </div>
                                    </td>
                                </tr>

                                {{-- Loop sản phẩm (Phần dưới này giữ nguyên như cũ) --}}
                                @foreach ($items as $item)
                                    @php
                                        $maxStock = $item['product']['stock'];
                                        $outOfStock = $maxStock == 0;
                                        $itemTotal = $item['price'] * ($quantities[$item['id']] ?? 0);
                                    @endphp

                                    <tr wire:key="cart-item-{{$item['id']}}" class="{{ $outOfStock ? 'table-danger' : 'bg-white' }}">
                                        <td class="text-center">
                                            <input type="checkbox" 
                                                class="form-check-input" 
                                                wire:model.live="selectedItems" 
                                                value="{{ $item['id'] }}" 
                                                {{ $outOfStock ? 'disabled' : '' }} />
                                        </td>
                                        
                                        <td style="width: 80px;">
                                            <img src="{{ !empty($item['product']['images']) ?
                                                        asset('storage/' . $item['product']['images'][0]['image_path']) :
                                                        asset('storage/product_images/default.jpg') }}" 
                                                class="img-fluid rounded" 
                                                style="width: 60px; height: 60px; object-fit: cover;">
                                        </td>
                                        
                                        <td>
                                            <a href="{{ route('products.detail', ['id' => $item['product']['id']]) }}" class="text-decoration-none text-dark">
                                                {{ $item['product']['name'] }}
                                            </a>
                                            
                                            @if($outOfStock)
                                                <br><span class="badge bg-danger">Hết hàng</span>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ number_format($item['price'], 0, ',', '.') }}₫</td>
                                        <td>
                                            @if(!$outOfStock)
                                                <input type="number" 
                                                    wire:model.live.debounce.500ms="quantities.{{ $item['id'] }}" 
                                                    min="1" 
                                                    max="{{ $maxStock }}" 
                                                    class="form-control form-control-sm mx-auto text-center" 
                                                    style="width: 70px;" />
                                            @endif
                                        </td>
                                        <td class="text-center fw-bold">{{ number_format($itemTotal, 0, ',', '.') }}₫</td>
                                        <td class="text-center">
                                            <button wire:click="removeItem({{ $item['id'] }})" class="btn btn-sm btn-link text-danger">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Footer --}}
            <div class="card-footer bg-white d-flex flex-column flex-md-row justify-content-between align-items-md-center sticky-bottom shadow" style="bottom: 0; z-index: 100;">
                
                <div class="d-flex align-items-center mb-2 mb-md-0">
                    <a href="{{ route('products.list') }}" class="btn btn-outline-primary me-3">
                        <i class="fas fa-arrow-left"></i> Tiếp tục mua hàng
                    </a>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="selectAllCheckbox" wire:model.live="selectAll">
                        <label class="form-check-label" for="selectAllCheckbox">
                            Chọn tất cả
                        </label>
                    </div>
                </div>
                
                <div class="d-flex align-items-center justify-content-end">
                    <span class="me-3 fs-5">
                        Tổng tiền ({{ count($selectedItems) }} sản phẩm):
                        <strong class="text-danger fs-4 ms-2">{{ number_format($this->totalPrice, 0, ',', '.') }}₫</strong>
                    </span>
                    <button wire:click="checkout" class="btn btn-success btn-lg" @if(empty($selectedItems)) disabled @endif>
                        Thanh toán
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>