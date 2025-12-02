<div>
    <div class="row">
        <div class="col-md-7">
            <h3>Quản lý Danh mục</h3>
            <button class="btn btn-primary mb-3" wire:click="createNewCategory">
                <i class="fas fa-plus"></i> Thêm danh mục mới
            </button>
            
            <div class="card">
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tên Danh mục</th>
                                <th>Số thuộc tính (Mẫu)</th>
                                <th style="width: 100px;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                                <tr wire:key="cat-{{ $category->id }}">
                                    <td>
                                        <strong>{{ $category->name }}</strong>
                                    </td>
                                    <td>
                                        {{-- Load 'attributes_count' cho hiệu năng --}}
                                        {{-- (Sửa loadCategories() trong PHP nếu muốn) --}}
                                        <span class="badge badge-info">{{ $category->attributes->count() }} thuộc tính</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info" 
                                                wire:click="editCategory({{ $category->id }})">
                                            Sửa
                                        </button>
                                        {{-- Thêm nút Xóa nếu muốn --}}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Chưa có danh mục nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-5">
            @if($showModal)
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h4>
                            @if($editingCategory->exists)
                                Sửa danh mục: {{ $editingCategory->name }}
                            @else
                                Tạo danh mục mới
                            @endif
                        </h4>
                    </div>
                    <div class="card-body">
                        <form wire:submit.prevent="saveCategory">
                            <div class="form-group">
                                <label>Tên</label>
                                <input type="text" 
                                       class="form-control @error('state.name') is-invalid @enderror" 
                                       wire:model.defer="state.name">
                                @error('state.name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            
                            <hr>

                            <h5>Gán Thuộc tính (Khuôn mẫu)</h5>
                            <div class="attribute-list" style="max-height: 250px; overflow-y: auto; border: 1px solid #eee; padding: 10px;">
                                @foreach($allAttributes as $attribute)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               value="{{ $attribute->id }}" 
                                               id="attr-{{ $attribute->id }}"
                                               wire:model.defer="selectedAttributes">
                                        <label class="form-check-label" for="attr-{{ $attribute->id }}">
                                            {{ $attribute->name }} ({{ $attribute->type }})
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            
                            <hr>

                            <h5>Gán Thương hiệu (Cho phép bán trong danh mục này)</h5>
                            <div class="brand-list" style="max-height: 200px; overflow-y: auto; border: 1px solid #eee; padding: 10px;">
                                <div class="row"> {{-- Dùng row/col để chia cột cho đẹp nếu nhiều brand --}}
                                    @foreach($allBrands as $brand)
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                {{-- Bind vào mảng selectedBrands --}}
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
                            <hr>

                            <div class="mb-4">
                                <label class="inline-flex items-center cursor-pointer">
                                    {{-- Map vào biến is_filterable trong Livewire --}}
                                    <input type="checkbox" wire:model="is_filterable" class="sr-only peer">
                                    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    <span class="ms-3 text-sm font-medium text-gray-900">Sử dụng làm bộ lọc tìm kiếm</span>
                                </label>
                            </div>

                            <hr>
                            
                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-success">
                                    <span wire:loading.remove wire:target="saveCategory">
                                        <i class="fas fa-save"></i> Lưu lại
                                    </span>
                                    <span wire:loading wire:target="saveCategory">
                                        <span class="spinner-border spinner-border-sm"></span> Đang lưu...
                                    </span>
                                </button>
                                <button type="button" class="btn btn-secondary" 
                                        wire:click="$set('showModal', false)">
                                    Hủy
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- XÓA TOÀN BỘ @push('scripts') VÀ CODE Sortable.js --}}