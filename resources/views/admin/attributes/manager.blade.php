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
                                        @if($attribute->unit)
                                            <small class="text-muted">({{ $attribute->unit }})</small>
                                        @endif
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
                                <label>Tên Thuộc tính</label>
                                <input type="text" 
                                       class="form-control @error('state.name') is-invalid @enderror" 
                                       wire:model.defer="state.name"
                                       placeholder="ví dụ: RAM, Kích thước màn hình...">
                                @error('state.name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="form-group mt-3">
                                <label>Loại (Type)</label>
                                <select class="form-control @error('state.type') is-invalid @enderror" 
                                        wire:model.live="state.type">
                                    <option value="text">Text (Người dùng tự nhập)</option>
                                    <option value="select">Select (Chọn từ danh sách)</option>
                                    <option value="number">Number (Nhập số)</option>
                                </select>
                                @error('state.type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            @if(isset($state['type']) && $state['type'] == 'number')
                            <div class="form-group mt-3" wire:key="unit-field">
                                <label>Đơn vị (Unit)</label>
                                <input type="text" 
                                       class="form-control @error('state.unit') is-invalid @enderror" 
                                       wire:model.defer="state.unit"
                                       placeholder="ví dụ: inch, kg, mAh, GB (để trống nếu không cần)">
                                @error('state.unit') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                <small class="form-text text-muted">Đây là hậu tố hiển thị cho người bán (ví dụ: 15.6 __inch__)</small>
                            </div>
                            @endif

                            @if(isset($state['type']) && $state['type'] == 'select')
                            <div class="mt-3" wire:key="options-field">
                                <label>Quản lý Tùy chọn (Options)</label>
                                <ul class="list-group mb-2">
                                    @forelse($options as $index => $option)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ $option['value'] }}
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    wire:click.prevent="removeOption({{ $index }})">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </li>
                                    @empty
                                        <li class="list-group-item text-muted">Chưa có tùy chọn nào.</li>
                                    @endforelse
                                </ul>
                                <div class="input-group">
                                    <input type="text" 
                                           class="form-control @error('newOptionValue') is-invalid @enderror" 
                                           wire:model="newOptionValue" 
                                           wire:keydown.enter.prevent="addOption"
                                           placeholder="ví dụ: 8GB, 16GB, S, M...">
                                    <button class="btn btn-outline-secondary" type="button" 
                                            wire:click.prevent="addOption">
                                        Thêm
                                    </button>
                                </div>
                                @error('newOptionValue') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                            </div>
                            @endif
                            
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