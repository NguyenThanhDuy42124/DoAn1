<div>
    <div class="row">
        {{-- 1. BẢNG DANH SÁCH: Full màn hình (col-12) --}}
        <div class="col-12">
            <h3>Quản lý Thương hiệu</h3>
            <button class="btn btn-primary mb-3" wire:click="createNewBrand">
                <i class="fas fa-plus"></i> Thêm thương hiệu mới
            </button>
            
            <div class="card shadow-sm">
                <div class="card-body">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">ID</th>
                                <th>Tên Thương hiệu</th>
                                <th class="text-end" style="width: 200px;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($brands as $brand)
                                <tr>
                                    <td>{{ $brand->id }}</td>
                                    <td>
                                        <strong>{{ $brand->name }}</strong>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-warning" 
                                                wire:click="editBrand({{ $brand->id }})">
                                            <i class="fas fa-edit"></i> Sửa
                                        </button>
                                        <button class="btn btn-sm btn-danger" 
                                                wire:click="deleteBrand({{ $brand->id }})" 
                                                wire:confirm="Xóa thương hiệu này?">
                                            <i class="fas fa-trash"></i> Xóa
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4">Chưa có thương hiệu nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    {{-- 2. MODAL GIỮA MÀN HÌNH (NO-JS) --}}
    @if($showModal)
        {{-- Backdrop tối màu --}}
        <div class="modal-backdrop fade show"></div>

        {{-- Khung Modal --}}
        <div class="modal fade show" tabindex="-1" style="display: block;" role="dialog" aria-modal="true">
            {{-- modal-dialog-centered: Căn giữa màn hình --}}
            <div class="modal-dialog modal-dialog-centered"> 
                <div class="modal-content shadow-lg">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            @if($editingBrand->exists) Sửa thương hiệu @else Tạo thương hiệu mới @endif
                        </h5>
                        {{-- Nút X gọi hàm closeModal --}}
                        <button type="button" class="btn-close btn-close-white" wire:click="closeModal"></button>
                    </div>
                    
                    <div class="modal-body">
                        <form wire:submit.prevent="saveBrand">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tên Thương hiệu</label>
                                <input type="text" 
                                       class="form-control @error('state.name') is-invalid @enderror" 
                                       wire:model.defer="state.name"
                                       placeholder="VD: Samsung, Apple...">
                                @error('state.name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </form>
                    </div>

                    <div class="modal-footer bg-light">
                        {{-- Nút Hủy gọi hàm closeModal --}}
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Hủy</button>
                        <button type="button" class="btn btn-success" wire:click="saveBrand">
                            <i class="fas fa-save"></i> Lưu lại
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>