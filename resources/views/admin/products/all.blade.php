<div>
    {{-- Header: Search + Filters + Export --}}
    <div>
        <fieldset>
            <legend>Bộ lọc và Tìm kiếm</legend>
            <div class="row g-3">
                <div class="col-md-12">
                    <label for="search">Tìm kiếm:</label>
                    <input type="text" id="search" wire:model.live.debounce.500ms="search"
                        class="form-control" placeholder="Tìm tên, mô tả, seller...">
                </div>

                <div class="col-md-3">
                    <label for="status">Trạng thái:</label>
                    <select id="status" wire:model.live="status" class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="pending">Chờ duyệt</option>
                        <option value="approved">Đã duyệt</option>
                        <option value="rejected">Bị từ chối</option>
                        <option value="hidden">Ẩn</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="category">Danh mục:</label>
                    <select id="category" wire:model.live="category_id" class="form-select">
                        <option value="">Tất cả danh mục</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="seller">Seller:</label>
                    <select id="seller" wire:model.live="seller_id" class="form-select">
                        <option value="">Tất cả seller</option>
                        @foreach($users as $seller)
                            <option value="{{ $seller->id }}">{{ $seller->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label>Khoảng giá:</label>
                    <div class="input-group">
                        <input type="number" wire:model.live="price_min" class="form-control" placeholder="Min">
                        <span class="input-group-text">-</span>
                        <input type="number" wire:model.live="price_max" class="form-control" placeholder="Max">
                    </div>
                </div>
                {{-- THANH CÔNG CỤ HÀNG LOẠT --}}
                @if(!empty($selected))
                <div class="alert alert-info d-flex justify-content-between align-items-center p-2 mb-3 shadow-sm fade show">
                    <div>
                        <i class="fas fa-check-square me-2"></i> Đã chọn <strong>{{ count($selected) }}</strong> sản phẩm
                    </div>
                    <div class="d-flex gap-2">
                        {{-- Nút Ẩn Hàng Loạt --}}
                        <button wire:click="openRejectModal(null, 'hidden')" class="btn btn-sm btn-warning">
                            <i class="fas fa-eye-slash"></i> Ẩn tất cả
                        </button>
                        
                        {{-- Nút Từ Chối Hàng Loạt --}}
                        <button wire:click="openRejectModal(null, 'reject')" class="btn btn-sm btn-danger">
                            <i class="fas fa-ban"></i> Từ chối tất cả
                        </button>
                        
                        {{-- Nút Khôi Phục Hàng Loạt (Approve) --}}
                        <button wire:click="openRejectModal(null, 'approve')" class="btn btn-sm btn-success">
                            <i class="fas fa-undo"></i> Khôi phục tất cả
                        </button>
                    </div>
                </div>
                @endif

                <div class="col-md-12 mt-3">
                    <button wire:click="export" class="btn btn-primary btn-sm me-2">
                       Export Excel
                    </button>
                    <button wire:click="$set('search', '')" class="btn btn-outline-danger btn-sm">
                       Xóa bộ lọc
                    </button>
                </div>
            </div>
        </fieldset>
    </div>

    <hr style="margin: 20px 0;">

    {{-- Quick Stats --}}
    <div class="mb-4">
        <h3>Thống kê nhanh</h3>
        <div class="row text-center">
             <div class="col">
                <div class="p-3 border rounded bg-light">
                    <h2>{{ $stats['total'] }}</h2>
                    <p class="mb-0 text-muted">Tổng sản phẩm</p>
                </div>
            </div>
            <div class="col">
                <div class="p-3 border rounded bg-light">
                    <h2 class="text-success">{{ $stats['approved_percent'] }}%</h2>
                    <p class="mb-0 text-muted">Tỷ lệ duyệt</p>
                </div>
            </div>
            <div class="col">
                <div class="p-3 border rounded bg-light">
                    <h2 class="text-warning">{{ $stats['pending'] }}</h2>
                    <p class="mb-0 text-muted">Chờ duyệt</p>
                </div>
            </div>
            <div class="col">
                <div class="p-3 border rounded bg-light">
                    <h2 class="text-danger">{{ $stats['rejected'] }}</h2>
                    <p class="mb-0 text-muted">Bị từ chối</p>
                </div>
            </div>
        </div>
    </div>

    <div>
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th wire:click="sortBy('name')" style="cursor: pointer;">
                            Tên sản phẩm @if($sortField === 'name') @if($sortDirection === 'asc') &uarr; @else &darr; @endif @endif
                        </th>
                        <th wire:click="sortBy('price')" style="cursor: pointer;">
                            Giá @if($sortField === 'price') @if($sortDirection === 'asc') &uarr; @else &darr; @endif @endif
                        </th>
                        <th>Danh mục</th>
                        <th>Seller</th>
                        <th wire:click="sortBy('status')" style="cursor: pointer;">
                            Trạng thái @if($sortField === 'status') @if($sortDirection === 'asc') &uarr; @else &darr; @endif @endif
                        </th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            {{-- UX CAO CẤP: Bấm tên là xem chi tiết luôn --}}
                            <td>{{ Str::limit($product->name, 40) }}</td> 
                            <td>{{ number_format($product->price) }}đ</td>
                            <td>{{ $product->category?->name ?? '-' }}</td>
                            <td>{{ $product->seller?->name ?? '-' }}</td>
                            
                            <td>
                                <span class="badge 
                                    @if(strtolower($product->status) == 'approved') bg-success 
                                    @elseif(strtolower($product->status) == 'rejected') bg-danger 
                                    @elseif(strtolower($product->status) == 'hidden') bg-secondary 
                                    @else bg-warning text-dark @endif">
                                    {{ ucfirst($product->status) }}
                                </span>
                            </td>
                            
                            {{-- CỘT HÀNH ĐỘNG --}}
                            <td>
                                <div class="d-flex gap-1">

                                    {{-- CÁC NÚT XỬ LÝ (Tùy trạng thái) --}}
                                    
                                    {{-- 1. Pending --}}
                                    @if(strtolower($product->status) == 'pending')
                                        <button wire:click="openRejectModal({{ $product->id }}, 'approve')" 
                                                class="btn btn-sm btn-success" title="Duyệt bài">
                                            Duyệt
                                        </button>
                                        <button wire:click="openRejectModal({{ $product->id }}, 'reject')" 
                                                class="btn btn-sm btn-danger" title="Từ chối">
                                            Từ chối
                                        </button>
                                    @endif

                                    {{-- 2. Approved --}}
                                    @if(strtolower($product->status) == 'approved')
                                        <button wire:click="openRejectModal({{ $product->id }}, 'hidden')" 
                                                class="btn btn-sm btn-warning" title="Ẩn sản phẩm này">
                                            Ẩn
                                        </button>
                                    @endif

                                    {{-- 3. Rejected / Hidden --}}
                                    @if(in_array(strtolower($product->status), ['rejected', 'hidden']))
                                        <button wire:click="openRejectModal({{ $product->id }}, 'approve')" 
                                                class="btn btn-sm btn-primary" title="Khôi phục lại">
                                            Khôi phục
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">Không có sản phẩm nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $products->links() }}
        </div>
    </div>
        
      

    {{-- MODAL (Đã cập nhật form input mới cho khớp PHP) --}}
    <div x-data="{ open: @entangle('reasonModalOpen') }">
        <div x-show="open" 
             style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 1050;"
             x-transition.opacity>

            <div class="card shadow-lg" 
                 style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 500px; max-width: 95%;">

                <div class="card-body p-4">
                    {{-- Tiêu đề --}}
                    <h5 class="card-title mb-3 fw-bold">
                        @if($actionType === 'reject') ⛔ Từ chối sản phẩm
                        @elseif($actionType === 'hidden') 🔒 Ẩn sản phẩm
                        @elseif($actionType === 'approve') ✅ Duyệt / Khôi phục
                        @endif
                    </h5>
                    <p class="text-muted small">ID sản phẩm: <strong>{{ $selectedProductId }}</strong></p>

                    {{-- NỘI DUNG FORM --}}
                    
                    {{-- Nếu là Approve: Chỉ hiện text xác nhận, ẩn ô nhập --}}
                    @if($actionType === 'approve')
                        <div class="alert alert-info">
                            Bạn có chắc chắn muốn duyệt/khôi phục sản phẩm này không?
                        </div>
                    @else
                    {{-- Nếu là Reject/Hidden: Hiện Form chọn lý do --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Lý do chính (*):</label>
                            {{-- Dùng reasonType (Select) --}}
                            <select wire:model="reasonType" class="form-select">
                                <option value="">-- Chọn lý do --</option>
                                @foreach($reasonOptions as $opt)
                                    <option value="{{ $opt }}">{{ $opt }}</option>
                                @endforeach
                            </select>
                            @error('reasonType') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ghi chú thêm:</label>
                            {{-- Dùng reasonNote (Textarea) --}}
                            <textarea wire:model="reasonNote" class="form-control" rows="3" placeholder="Nhập chi tiết..."></textarea>
                        </div>
                    @endif

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button wire:click="closeRejectModal" class="btn btn-secondary">Hủy</button>
                        <button wire:click="confirmReject" 
                            class="btn @if($actionType == 'approve') btn-success @elseif($actionType == 'hidden') btn-warning @else btn-danger @endif">
                            Xác nhận
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>