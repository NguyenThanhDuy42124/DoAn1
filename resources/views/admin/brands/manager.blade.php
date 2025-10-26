<div>
    <div class="row">
        <div class="col-md-7">
            <h3>Quản lý Thương hiệu</h3> <button class="btn btn-primary mb-3" wire:click="createNewBrand"> <i class="fas fa-plus"></i> Thêm thương hiệu mới
            </button>
            
            <div class="card shadow-sm">
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên (Name)</th>
                                <th>Hành động</th> </tr>
                        </thead>
                        <tbody>
                            @forelse($brands as $brand) <tr>
                                    <td>{{ $brand->id }}</td>
                                    <td>{{ $brand->name }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" 
                                                wire:click="editBrand({{ $brand->id }})"> <i class="fas fa-edit"></i> Sửa
                                        </button>
                                        <button class="btn btn-sm btn-danger" 
                                                wire:click="deleteBrand({{ $brand->id }})" wire:confirm="Mày chắc chắn muốn xóa thương hiệu này?">
                                            <i class="fas fa-trash"></i> Xóa
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Chưa có thương hiệu nào.</td> </tr>
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
                            @if($editingBrand->exists) Sửa thương hiệu: {{ $editingBrand->name }} @else
                                Tạo thương hiệu mới
                            @endif
                        </h4>
                    </div>
                    <div class="card-body">
                        <form wire:submit.prevent="saveBrand"> <div class="form-group">
                                <label>Tên Thương hiệu (ví dụ: Samsung, Apple...)</label>
                                <input type="text" 
                                       class="form-control @error('state.name') is-invalid @enderror" 
                                       wire:model.defer="state.name"
                                       placeholder="ví dụ: Samsung">
                                @error('state.name') <span class="invalid-feedback">{{ $message }}</span> @enderror
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