<div>
    <div class="row">
        <div class="col-md-7">
            <h3>Quản lý Thuộc tính</h3>
            <button class="btn btn-primary mb-3" wire:click="createNewAttribute">
                <i class="fas fa-plus"></i> Thêm thuộc tính mới
            </button>
            
            <div class="card shadow-sm">
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên (Name)</th>
                                <th>Loại (Type)</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allAttributes as $attribute)
                                <tr>
                                    <td>{{ $attribute->id }}</td>
                                    <td>{{ $attribute->name }}</td>
                                    <td>
                                        <span class="badge 
                                            @if($attribute->type == 'text') badge-info
                                            @elseif($attribute->type == 'select') badge-success
                                            @else badge-warning @endif">
                                            {{ $attribute->type }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" 
                                                wire:click="editAttribute({{ $attribute->id }})">
                                            <i class="fas fa-edit"></i> Sửa
                                        </button>
                                        <button class="btn btn-sm btn-danger" 
                                                wire:click="deleteAttribute({{ $attribute->id }})"
                                                wire:confirm="Mày chắc chắn muốn xóa thuộc tính này? (Sẽ bị lỗi nếu nó đang được gán cho danh mục)">
                                            <i class="fas fa-trash"></i> Xóa
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Chưa có thuộc tính nào.</td>
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
                            @if($editingAttribute->exists)
                                Sửa thuộc tính: {{ $editingAttribute->name }}
                            @else
                                Tạo thuộc tính mới
                            @endif
                        </h4>
                    </div>
                    <div class="card-body">
                        <form wire:submit.prevent="saveAttribute">
                            <div class="form-group">
                                <label>Tên Thuộc tính (ví dụ: RAM, CPU, Size...)</label>
                                <input type="text" 
                                       class="form-control @error('state.name') is-invalid @enderror" 
                                       wire:model.defer="state.name"
                                       placeholder="ví dụ: RAM">
                                @error('state.name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="form-group mt-3">
                                <label>Loại (Type)</label>
                                <select class="form-control @error('state.type') is-invalid @enderror" 
                                        wire:model.defer="state.type">
                                    <option value="">— Chọn loại —</option>
                                    <option value="text">Text (Người dùng tự nhập, ví dụ: Core i7)</option>
                                    <option value="select">Select (Chọn từ danh sách, ví dụ: 8GB, 16GB)</option>
                                    <option value="number">Number (Chỉ cho nhập số, ví dụ: 15.6 inch)</option>
                                </select>
                                @error('state.type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            
                            <hr>
                            
                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-success">
                                    Lưu lại
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