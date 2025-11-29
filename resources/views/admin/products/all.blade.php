<div>
    {{-- Header: Search + Filters + Export --}}
    <div>
        <fieldset>
            <legend>Bộ lọc và Tìm kiếm</legend>
            <div>
                <label for="search">Tìm kiếm:</label>
                <input type="text" id="search" wire:model.live.debounce.500ms="search"
                    placeholder="Tìm tên, mô tả, seller...">
            </div>

            <div style="margin-top: 10px;">
                <label for="status">Trạng thái:</label>
                <select id="status" wire:model.live="status">
                    <option value="">Tất cả trạng thái</option>
                    <option value="pending">Chờ duyệt</option>
                    <option value="approved">Đã duyệt</option>
                    <option value="rejected">Bị từ chối</option>
                    <option value="hidden">Ẩn</option>
                </select>
            </div>

            <div style="margin-top: 10px;">
                <label for="category">Danh mục:</label>
                <select id="category" wire:model.live="category_id">
                    <option value="">Tất cả danh mục</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-top: 10px;">
                <label for="seller">Seller:</label>
                <select id="seller" wire:model.live="seller_id">
                    <option value="">Tất cả seller</option>
                    @foreach($users as $seller)
                        <option value="{{ $seller->id }}">{{ $seller->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-top: 10px;">
                <label for="price_min">Giá từ:</label>
                <input type="number" id="price_min" wire:model.live="price_min" placeholder="Giá từ" style="width: 80px;">
                <span>-</span>
                <label for="price_max">Giá đến:</label>
                <input type="number" id="price_max" wire:model.live="price_max" placeholder="Giá đến" style="width: 80px;">
            </div>

            <div style="margin-top: 15px;">
                <button wire:click="export" class="btn btn-primary btn-sm me-2">
                   Export Excel
                </button>

                <button wire:click="$set('search', '')" class="btn btn-danger btn-sm">
                   Xóa bộ lọc
                </button>
            </div>
        </fieldset>
    </div>

    <hr style="margin: 20px 0;">

    {{-- Quick Stats --}}
    <div>
        <h3>Thống kê nhanh</h3>
        <div style="display: flex; gap: 20px;">
             <div>
                <h2>{{ $stats['total'] }}</h2>
                <p>Tổng sản phẩm</p>
            </div>
            <div>
                <h2>{{ $stats['approved_percent'] }}%</h2>
                <p>Đã duyệt</p>
            </div>
            <div>
                <h2>{{ $stats['pending'] }}</h2>
                <p>Chờ duyệt</p>
            </div>
            <div>
                <h2>{{ $stats['rejected'] }}</h2>
                <p>Bị từ chối</p>
            </div>
        </div>
    </div>

    {{-- Table --}}
    {{-- Table --}}
<div>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th wire:click="sortBy('name')" class="border px-4 py-2" style="cursor: pointer;">
                        Tên sản phẩm @if($sortField === 'name') @if($sortDirection === 'asc') &uarr; @else &darr; @endif @endif
                    </th>
                    <th wire:click="sortBy('price')" class="border px-4 py-2" style="cursor: pointer;">
                        Giá @if($sortField === 'price') @if($sortDirection === 'asc') &uarr; @else &darr; @endif @endif
                    </th>
                    <th class="border px-4 py-2">Danh mục</th>
                    <th class="border px-4 py-2">Seller</th>
                    <th wire:click="sortBy('status')" class="border px-4 py-2" style="cursor: pointer;">
                        Trạng thái @if($sortField === 'status') @if($sortDirection === 'asc') &uarr; @else &darr; @endif @endif
                    </th>
                    {{-- SỬA LẠI CHỖ NÀY: Chỉ để tiêu đề thôi --}}
                    <th class="border px-4 py-2">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td class="border px-4 py-2">{{ Str::limit($product->name, 40) }}</td>
                        <td class="border px-4 py-2">{{ number_format($product->price) }}đ</td>
                        <td class="border px-4 py-2">{{ $product->category?->name ?? '-' }}</td>
                        <td class="border px-4 py-2">{{ $product->seller?->name ?? '-' }}</td>
                        <td class="border px-4 py-2">
                            {{-- Badge màu mè cho đẹp --}}
                            <span class="badge 
                                @if($product->status == 'approved') bg-success 
                                @elseif($product->status == 'rejected') bg-danger 
                                @elseif($product->status == 'hidden') bg-secondary 
                                @else bg-warning text-dark @endif">
                                {{ ucfirst($product->status) }}
                            </span>
                        </td>
                        
                        {{-- SỬA LẠI CHỖ NÀY: Logic hiển thị nút --}}
                        <td class="border px-4 py-2">
                            <div class="d-flex gap-1">
                                {{-- Case: Pending --}}
                                @if($product->status === 'pending')
                                    <button wire:click="openRejectModal({{ $product->id }}, 'approve')" class="btn btn-sm btn-success">Duyệt</button>
                                    <button wire:click="openRejectModal({{ $product->id }}, 'reject')" class="btn btn-sm btn-danger">Từ chối</button>
                                @endif

                                {{-- Case: Approved --}}
                                @if($product->status === 'approved')
                                    <button wire:click="openRejectModal({{ $product->id }}, 'hidden')" class="btn btn-sm btn-warning">Ẩn</button>
                                @endif

                                {{-- Case: Rejected / Hidden --}}
                                @if(in_array($product->status, ['rejected', 'hidden']))
                                    <button wire:click="openRejectModal({{ $product->id }}, 'approve')" class="btn btn-sm btn-primary">Khôi phục</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td class="border px-4 py-2">
                                {{ Str::limit($product->name, 50) }}
                            </td>
                            <td class="border px-4 py-2">
                                {{ number_format($product->price) }}đ
                            </td>
                            <td class="border px-4 py-2">
                                {{ $product->category?->name ?? '-' }}
                            </td>
                            <td class="border px-4 py-2">
                                {{ $product->seller?->name ?? '-' }}
                            </td>
                            <td class="border px-4 py-2">
                                <span>
                                    {{ ucfirst($product->status) }}
                                </span>
                            </td>
                            <td class="border px-4 py-2">

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="border px-4 py-2" style="text-align: center;">Không có sản phẩm nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 15px;">
            {{ $products->links('pagination::bootstrap-5') }}

        </div>

    </div>
    
    <div class="mt-3">
        {{ $products->links() }}
    </div>
</div>
        {{-- Modal cho reason (Fix lỗi căn giữa) --}}
    {{-- Modal --}}
<div x-data="{ open: @entangle('reasonModalOpen') }">
    <div x-show="open" 
         style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 1050;"
         x-transition.opacity>

        <div class="card shadow-lg" 
             style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 400px; max-width: 90%;">

            <div class="card-body p-4">
                {{-- Tiêu đề động dựa vào actionType --}}
                <h5 class="card-title mb-3 fw-bold">
                    @if($actionType === 'reject')
                        ⛔ Từ chối sản phẩm
                    @elseif($actionType === 'hidden')
                        🔒 Ẩn sản phẩm
                    @elseif($actionType === 'approve')
                        ✅ Duyệt / Khôi phục
                    @endif
                </h5>

                <p class="text-muted small">ID sản phẩm: <strong>{{ $selectedProductId }}</strong></p>

                {{-- BỎ CÁI SELECT ACTION TYPE ĐI, KHÔNG CẦN THIẾT --}}

                <div class="mb-3">
                    <label class="form-label">Lý do / Ghi chú:</label>
                    <textarea wire:model="reason" class="form-control" rows="4" placeholder="Nhập nội dung..."></textarea>
                    
                    {{-- QUAN TRỌNG: Hiển thị lỗi nếu quên nhập --}}
                    @error('reason') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button wire:click="closeRejectModal" class="btn btn-secondary">Hủy</button>
                    
                    {{-- Nút xác nhận đổi màu theo hành động --}}
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
