<div>
    {{-- Nút bấm chính --}}
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-2">
        
        {{-- Nút Duyệt --}}
        <button class="btn btn-success" wire:click="approve">
            <i class="fas fa-check"></i> Duyệt bài
        </button>
        
        {{-- Nút Từ chối --}}
        <button class="btn btn-danger" wire:click="openRejectModal('reject')">
            <i class="fas fa-times"></i> Từ chối
        </button>

        {{-- Nút Ẩn (Nếu cần) --}}
        {{-- <button class="btn btn-warning" wire:click="openRejectModal('hidden')">Ẩn</button> --}}

        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-primary ms-auto">
            ← Quay lại danh sách
        </a>
    </div>

    {{-- Modal lý do từ chối (Dùng Alpine x-show) --}}
    <div x-data="{ open: @entangle('reasonModalOpen') }" 
         x-show="open" 
         style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 1050;" 
         x-transition.opacity>
         
        <div class="card shadow-lg bg-white" 
             style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 450px; max-width: 90%;">
             
            <div class="card-body p-4">
                <h5 class="card-title mb-3 fw-bold text-danger">
                    {{ $actionType == 'hidden' ? '🔒 Ẩn sản phẩm' : '⛔ Từ chối sản phẩm' }}
                </h5>
                <h6 class="card-subtitle mb-3 text-muted">ID sản phẩm: <strong>{{ $productId }}</strong></h6>

                {{-- Chọn lý do mẫu --}}
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

                {{-- Nhập thêm ghi chú --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">2. Ghi chú thêm:</label>
                    <textarea wire:model="reasonNote" class="form-control" rows="3" placeholder="Chi tiết lỗi..."></textarea>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button wire:click="closeModal" class="btn btn-secondary">Hủy</button>
                    <button wire:click="confirmReject" class="btn btn-danger">Xác nhận</button>
                </div>
            </div>
        </div>
    </div>
</div>