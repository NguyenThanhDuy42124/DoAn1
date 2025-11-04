<div>
    {{-- Hai nút chính --}}
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-2">
        {{-- Thay $product->id thành $productId --}}

        <button class="btn btn-success" wire:click="approve({{ $productId }})" ...>
            Duyệt
        </button>
        <button class="btn btn-danger" wire:click="openRejectModal({{ $productId }})" ...>
            Từ chối
        </button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-primary ms-auto">
            ← Trở về trang quản trị
        </a>
    </div>

    {{-- Modal lý do từ chối --}}
    <div x-data="{ open: @entangle('reasonModalOpen') }" x-show="open" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%;
               background-color: rgba(0,0,0,0.5); z-index:1050;" x-transition>
        <div class="card shadow-lg col-md-4 bg-white" style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%);">
            <div class="card-body p-4">
                <h5 class="card-title mb-3">Lý do từ chối sản phẩm</h5>
                <h6 class="card-subtitle mb-3 text-muted">
                    ID sản phẩm: <strong>{{ $selectedProductId }}</strong>
                </h6>

                <div class="mb-3">
                    <label class="form-label">Hành động:</label>
                    <select wire:model="actionType" class="form-select">
                        <option value="reject">Từ chối</option>
                        <option value="hidden">Ẩn sản phẩm</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Lý do:</label>
                    <textarea wire:model="reason" class="form-control" rows="4" placeholder="Nhập lý do..."></textarea>
                    @error('reason')
                    <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <div class="d-flex justify-content-end">
                    <button wire:click="closeRejectModal" class="btn btn-secondary me-2">
                        Hủy
                    </button>
                    <button wire:click="confirmReject" class="btn btn-danger">
                        Xác nhận
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
