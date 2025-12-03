<div>
    <div class="row">
        {{-- 1. BẢNG DANH SÁCH: Full màn hình --}}
        <div class="col-12">
            <h3>Quản lý Danh mục</h3>
            <button class="btn btn-primary mb-3" wire:click="createNewCategory">
                <i class="fas fa-plus"></i> Thêm danh mục mới
            </button>
            
            <div class="card shadow-sm">
                <div class="card-body">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Tên Danh mục</th>
                                <th>Thống kê</th>
                                <th class="text-end">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                                <tr wire:key="cat-{{ $category->id }}">
                                    <td>
                                        <strong>{{ $category->name }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $category->attributes_count }} thuộc tính</span>
                                        <span class="badge bg-secondary">{{ $category->brands_count }} thương hiệu</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" 
                                                wire:click="editCategory({{ $category->id }})">
                                            <i class="fas fa-edit">Sửa</i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" 
                                                wire:click="deleteCategory({{ $category->id }})" 
                                                wire:confirm="Xóa danh mục này sẽ ảnh hưởng đến sản phẩm. Chắc chắn xóa?">
                                            <i class="fas fa-trash">Xóa</i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4">Chưa có danh mục nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. MODAL GIAO DIỆN MỚI --}}
    @if($showModal)
        {{-- Backdrop tối màu --}}
        <div class="modal-backdrop fade show"></div>

        {{-- Modal chính --}}
        <div class="modal fade show" tabindex="-1" style="display: block;" role="dialog" aria-modal="true">
            {{-- modal-xl cho rộng để chứa 2 cột attributes và brands --}}
            <div class="modal-dialog modal-xl modal-dialog-centered"> 
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            @if($editingCategory->exists) Sửa danh mục @else Tạo danh mục mới @endif
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeModal"></button>
                    </div>
                    
                    <div class="modal-body">
                        <form wire:submit.prevent="saveCategory">
                            {{-- Tên Danh mục --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold">Tên Danh mục</label>
                                <input type="text" class="form-control @error('state.name') is-invalid @enderror" 
                                       wire:model.defer="state.name" placeholder="VD: Điện thoại, Laptop...">
                                @error('state.name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="row">
                                {{-- CỘT TRÁI: THUỘC TÍNH --}}
                                <div class="col-md-6">
                                    <div class="card h-100 border-info">
                                        <div class="card-header bg-info text-white">
                                            <i class="fas fa-list"></i> Gán Thuộc tính (Khuôn mẫu)
                                        </div>
                                        <div class="card-body p-0">
                                            <div style="max-height: 300px; overflow-y: auto; padding: 10px;">
                                                @foreach($allAttributes as $attribute)
                                                    <div class="form-check mb-1">
                                                        <input class="form-check-input" type="checkbox" 
                                                               value="{{ $attribute->id }}" 
                                                               id="attr-{{ $attribute->id }}"
                                                               wire:model.defer="selectedAttributes">
                                                        <label class="form-check-label" for="attr-{{ $attribute->id }}">
                                                            {{ $attribute->name }} <small class="text-muted">({{ $attribute->type }})</small>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- CỘT PHẢI: THƯƠNG HIỆU --}}
                                <div class="col-md-6">
                                    <div class="card h-100 border-secondary">
                                        <div class="card-header bg-secondary text-white">
                                            <i class="fas fa-tags"></i> Gán Thương hiệu
                                        </div>
                                        <div class="card-body p-0">
                                            <div style="max-height: 300px; overflow-y: auto; padding: 10px;">
                                                <div class="row">
                                                    @foreach($allBrands as $brand)
                                                        <div class="col-6">
                                                            <div class="form-check mb-1">
                                                                <input class="form-check-input" type="checkbox" 
                                                                    value="{{ $brand->id }}" 
                                                                    id="brand-{{ $brand->id }}"
                                                                    wire:model.defer="selectedBrands">
                                                                <label class="form-check-label" for="brand-{{ $brand->id }}">
                                                                    {{ $brand->name }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                    
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Hủy</button>
                        <button type="button" class="btn btn-success" wire:click="saveCategory">
                            <i class="fas fa-save"></i> Lưu lại
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>