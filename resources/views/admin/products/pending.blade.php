<div>
    {{-- Search --}}
    <div class="mb-4">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Tìm kiếm..." class="border p-2 w-full rounded form-control">
    </div>

    {{-- KHU VỰC NÚT HÀNG LOẠT (Đã xóa thẻ Form) --}}
    <div class="mb-4 p-3 bg-light border rounded d-flex align-items-center justify-content-between">
        <div>
            <span class="fw-bold me-2">Đã chọn:</span>
            <span class="badge bg-primary rounded-pill">{{ count($selected) }}</span>
        </div>
        
        <div class="d-flex gap-2">
            {{-- Nút Duyệt: Gọi thẳng hàm bulkApprove --}}
            <button wire:click="bulkApprove" 
                    class="btn btn-success"
                    @if(empty($selected)) disabled @endif>
                <i class="fas fa-check"></i> Duyệt tất cả chọn
            </button>

            {{-- Nút Từ chối: Gọi hàm mở Modal --}}
            <button wire:click="openBulkRejectModal" 
                    class="btn btn-danger"
                    @if(empty($selected)) disabled @endif>
                <i class="fas fa-times"></i> Từ chối tất cả chọn
            </button>
        </div>
    </div>

    {{-- Modal cho reason (dùng x-data Alpine) --}}
    <div x-data="{ open: false }" class="mb-4">
        <input type="text" wire:model="reason" placeholder="Lý do từ chối (cho bulk)" class="border p-2 w-full rounded" x-show="open">
    </div>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="border px-4 py-2"><input type="checkbox" wire:model.live="selectAll"></th>
                    <th class="border px-4 py-2">ID</th>
                    <th class="border px-4 py-2">Tên</th>
                    <th class="border px-4 py-2">Giá</th>
                    <th class="border px-4 py-2">Seller</th>
                    <th class="border px-4 py-2">Danh mục</th>
                    <th class="border px-4 py-2">Ngày đăng</th>
                    <th class="border px-4 py-2">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                    <td class="border px-4 py-2"><input type="checkbox" wire:model.live="selected" value="{{ $product->id }}"></td>
                    <td class="border px-4 py-2">{{ $product->id }}</td>
                    <td class="border px-4 py-2">
                        {{ $product->name }}
                        {{-- Preview modal: Thêm Alpine hoặc Livewire modal để show desc/images --}}
                    </td>
                    <td class="border px-4 py-2">{{ number_format($product->price) }} VND</td>
                    <td class="border px-4 py-2">{{ $product->seller->name }}</td>
                    <td class="border px-4 py-2">{{ $product->category->name }}</td>
                    <td class="border px-4 py-2">{{ $product->created_at->format('d/m/Y') }}</td>
                    <td class="border px-4 py-2">
                        <div class="d-flex flex-column flex-md-row">

                            <button wire:click="approve({{ $product->id }})" class="bg-green-500 btn-primary px-2 py-1 rounded mb-1 mb-md-0 me-md-1">
                                Duyệt
                            </button>

                            <button wire:click="openRejectModal({{ $product->id }})" class="bg-red-500 btn-danger px-2 py-1 rounded">
                                Từ chối
                            </button>
                            <a href="{{ route('products.detail', ['id' => $product->id]) }}" class="btn btn-outline-primary btn-sm me-2">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $products->links() }}
    {{-- MODAL ĐÃ SỬA LẠI (QUAN TRỌNG) --}}
    {{-- MODAL ĐA NĂNG (Xử lý cả Lẻ và Hàng loạt) --}}
    <div x-data="{ open: @entangle('reasonModalOpen') }">
        <div x-show="open" 
             style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 1050;"
             x-transition.opacity>

            <div class="card shadow-lg" 
                 style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 500px; max-width: 95%;">

                <div class="card-body p-4">
                    {{-- Tiêu đề Modal --}}
                    <h5 class="card-title mb-3 fw-bold text-danger">
                        @if($isBulk)
                            ⛔ Từ chối hàng loạt
                        @else
                            {{ $actionType == 'reject' ? '⛔ Từ chối sản phẩm' : ($actionType == 'hidden' ? '🔒 Ẩn sản phẩm' : '✅ Duyệt sản phẩm') }}
                        @endif
                    </h5>

                    {{-- Thông tin đối tượng bị xử lý --}}
                    <div class="alert alert-light border mb-3">
                        @if($isBulk)
                            <p class="mb-0">Bạn đang chọn từ chối <strong>{{ count($selected) }}</strong> sản phẩm.</p>
                            <small class="text-muted">Lý do bên dưới sẽ được gửi cho tất cả người bán tương ứng.</small>
                        @else
                            <p class="mb-0">Đang xử lý sản phẩm ID: <strong>{{ $selectedProductId }}</strong></p>
                        @endif
                    </div>

                    {{-- Form Nhập Lý Do --}}
                    @if($actionType !== 'approve')
                        <div class="mb-3">
                            <label class="form-label fw-bold">1. Lý do chính (*):</label>
                            <select wire:model="reasonType" class="form-select">
                                <option value="">-- Chọn lý do --</option>
                                @foreach($reasonOptions as $opt)
                                    <option value="{{ $opt }}">{{ $opt }}</option>
                                @endforeach
                            </select>
                            @error('reasonType') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">2. Ghi chú chi tiết:</label>
                            <textarea wire:model="reasonNote" class="form-control" rows="3" placeholder="Nhập thêm chi tiết..."></textarea>
                        </div>
                    @else
                        <p>Xác nhận duyệt sản phẩm này?</p>
                    @endif

                    {{-- Footer Buttons --}}
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button wire:click="closeRejectModal" class="btn btn-secondary">Hủy</button>
                        
                        <button wire:click="confirmReject" 
                            class="btn {{ $actionType == 'approve' ? 'btn-success' : 'btn-danger' }}">
                            {{ $isBulk ? 'Xác nhận (Hàng loạt)' : 'Xác nhận' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
