
<div class="container py-4">
    {{-- CSS RIÊNG CHO CARD SẢN PHẨM (Để đảm bảo giống mẫu mày thích) --}}
    <style>
        .modern-product-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #eee;
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            position: relative;
        }
        .modern-product-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transform: translateY(-2px);
            border-color: #bce3c9;
        }
        .product-img-wrapper {
            position: relative;
            padding-top: 100%; /* Ratio 1:1 */
            overflow: hidden;
        }
        .modern-product-img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 10px;
            transition: transform 0.5s;
        }
        .modern-product-card:hover .modern-product-img {
            transform: scale(1.05);
        }
        .modern-discount-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: #ff3b30;
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: bold;
            z-index: 2;
        }
        .product-body {
            padding: 15px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        .product-title {
            font-size: 1rem;
            font-weight: 600;
            color: #253D4E;
            margin-bottom: 5px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 2.4em; /* Giới hạn 2 dòng */
        }
        .product-price-highlight {
            color: #3BB77E;
            font-size: 1.1rem;
            font-weight: bold;
        }
        .product-footer {
            padding: 10px 15px;
            background: #fff;
            border-top: 1px solid #f0f0f0;
        }
        /* Custom Scrollbar cho bộ lọc */
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #ccc; border-radius: 5px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #aaa; }
    </style>

    <div class="row">
        {{-- ================================================= --}}
        {{-- CỘT TRÁI: BỘ LỌC (SIDEBAR FILTER) --}}
        {{-- ================================================= --}}
        <div class="col-lg-3 mb-4">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-funnel"></i> Bộ lọc</h5>
                </div>
                <div class="card-body p-0">
                    
                    {{-- 1. KHOẢNG GIÁ --}}
                    <div class="p-3 border-bottom">
                        <h6 class="fw-bold mb-3">Khoảng giá</h6>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <button class="btn btn-sm w-100 {{ $priceRange == '0-5000000' ? 'btn-success' : 'btn-outline-secondary' }}" 
                                    wire:click="setPrice('0-5000000')">Dưới 5 triệu</button>
                            <button class="btn btn-sm w-100 {{ $priceRange == '5000000-10000000' ? 'btn-success' : 'btn-outline-secondary' }}" 
                                    wire:click="setPrice('5000000-10000000')">5 - 10 triệu</button>
                            <button class="btn btn-sm w-100 {{ $priceRange == '10000000-20000000' ? 'btn-success' : 'btn-outline-secondary' }}" 
                                    wire:click="setPrice('10000000-20000000')">10 - 20 triệu</button>
                            <button class="btn btn-sm w-100 {{ $priceRange == '20000000-999999999' ? 'btn-success' : 'btn-outline-secondary' }}" 
                                    wire:click="setPrice('20000000-999999999')">Trên 20 triệu</button>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <input type="number" class="form-control form-control-sm" placeholder="Min" wire:model.live.debounce.800ms="minPrice">
                            <span>-</span>
                            <input type="number" class="form-control form-control-sm" placeholder="Max" wire:model.live.debounce.800ms="maxPrice">
                        </div>
                    </div>

                    {{-- 2. THƯƠNG HIỆU --}}
                    <div class="p-3 border-bottom">
                        <h6 class="fw-bold mb-2">Thương hiệu</h6>
                        <div class="custom-scrollbar" style="max-height: 200px; overflow-y: auto;">
                            @foreach($categoryBrands as $brand)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" 
                                           value="{{ $brand->id }}" 
                                           wire:model.live="selectedBrands" 
                                           id="brand-{{ $brand->id }}">
                                    <label class="form-check-label" for="brand-{{ $brand->id }}">
                                        {{ $brand->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- 3. THUỘC TÍNH ĐỘNG (JSON) --}}
                    @foreach($filterableAttributes as $attribute)
                        <div class="p-3 border-bottom">
                            <h6 class="fw-bold mb-2">{{ $attribute->name }}</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($attribute->options as $option)
                                    @php
                                        $val = $option->value;
                                        // Fix hiển thị Label: Ưu tiên lấy label, nếu rỗng mới lấy value
                                        $label = $option->label ? $option->label : $val; 
                                    @endphp
                                    
                                    {{-- QUAN TRỌNG: Thêm wire:key vào div bao ngoài --}}
                                    <div class="form-check w-100 mb-1" wire:key="attr-{{ $attribute->id }}-opt-{{ $option->id }}">
                                        <input class="form-check-input" type="checkbox" 
                                            value="{{ $val }}" 
                                            wire:model.live="selectedAttributes.{{ $attribute->id }}"
                                            id="opt-{{ $option->id }}">
                                        
                                        <label class="form-check-label" for="opt-{{ $option->id }}">
                                            {{ $label }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    {{-- NÚT RESET --}}
                    <div class="p-3">
                        <button class="btn btn-outline-danger w-100" wire:click="$set('selectedAttributes', [])">
                            <i class="bi bi-arrow-counterclockwise"></i> Xóa bộ lọc
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================================================= --}}
        {{-- CỘT PHẢI: DANH SÁCH SẢN PHẨM --}}
        {{-- ================================================= --}}
        <div class="col-lg-9">
            
            {{-- Loading khi đang lọc --}}
            <div wire:loading class="alert alert-info w-100 shadow-sm border-0 mb-3">
                <i class="fas fa-spinner fa-spin me-2"></i> Đang cập nhật kết quả...
            </div>

            <div class="row g-3">
                @forelse($products as $product)
                <div class="col-6 col-md-4"> 
                    {{-- CARD SẢN PHẨM (Y HỆT MẪU CỦA MÀY) --}}
                    <div class="modern-product-card">
                        
                        <a href="{{ route('products.detail', ['id' => $product->id]) }}" class="text-decoration-none text-dark d-contents">
                            {{-- Ảnh --}}
                            <div class="product-img-wrapper">
                                @if($product->original_price && $product->original_price > $product->price)
                                    <div class="modern-discount-badge">
                                        -{{ round(100 - ($product->price / $product->original_price * 100)) }}%
                                    </div>
                                @endif

                                <img src="{{ $product->images->isNotEmpty() ? 
                                            asset('storage/' . $product->images->first()->image_path) : 
                                            asset('storage/product_images/default.jpg') }}" 
                                     alt="{{ $product->name }}" 
                                     class="modern-product-img">
                            </div>

                            {{-- Body --}}
                            <div class="product-body">
                                <h5 class="product-title" title="{{ $product->name }}">
                                    {{ $product->name }}
                                </h5>
                                
                                <div class="mb-2">
                                    <span class="badge bg-light text-dark border fw-normal me-1">
                                        {{ Str::limit($product->category->name ?? 'Khác', 10) }}
                                    </span>
                                    <small class="text-muted">
                                        {{ $product->brand->name ?? '' }}
                                    </small>
                                </div>

                                <div class="mt-auto">
                                    <span class="product-price-highlight">
                                        {{ number_format($product->price, 0, ',', '.') }}₫
                                    </span>
                                    @if ($product->original_price > $product->price)
                                        <span class="text-muted text-decoration-line-through small ms-1" style="font-size: 0.8rem;">
                                            {{ number_format($product->original_price, 0, ',', '.') }}₫
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </a>

                        {{-- Footer (Nút bấm) --}}
                        <div class="product-footer">
                            <div class="d-flex gap-2">
                                <a href="{{ route('products.detail', ['id' => $product->id]) }}" 
                                   class="btn btn-outline-primary btn-sm" 
                                   title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <div class="flex-grow-1">
                                    {{-- GIỮ NGUYÊN LOGIC CHECK AUTH CỦA MÀY --}}
                                    @if (Auth::check())
                                        @if (empty(Auth::user()->phoneNumber) || empty(Auth::user()->address))
                                            <a href="{{ route('general.users.edit', Auth::user()->id) }}" 
                                               class="btn btn-warning btn-sm w-100 text-white">
                                                <i class="fas fa-user-edit"></i> Cập nhật
                                            </a>
                                        @else
                                            @if($product->stock > 0)
                                                <form action="{{ route('buyer.carts.add') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                    <input type="hidden" name="quantity" value="1">
                                                    <button type="submit" class="btn btn-success btn-sm w-100">
                                                        <i class="fas fa-cart-plus"></i> Thêm
                                                    </button>
                                                </form>
                                            @else
                                                <button class="btn btn-secondary btn-sm w-100" disabled>Hết hàng</button>
                                            @endif
                                        @endif
                                    @else
                                        <a href="{{ route('login') }}" class="btn btn-success btn-sm w-100">
                                            <i class="fas fa-cart-plus"></i> Thêm
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                    {{-- END CARD --}}
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <i class="bi bi-emoji-frown fs-1 text-muted"></i>
                    <p class="mt-3 text-muted">Không tìm thấy sản phẩm nào phù hợp với bộ lọc.</p>
                </div>
                @endforelse
            </div>

            {{-- Phân trang Livewire --}}
            <div class="mt-4">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>