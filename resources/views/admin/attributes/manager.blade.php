<div>
    {{-- PHẦN DANH SÁCH (FULL MÀN HÌNH) --}}
    <div class="row">
        <div class="col-md-12">
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
                                <th style="width: 200px;">Hành động</th>
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
                                                wire:confirm="Mày chắc chắn muốn xóa thuộc tính này?">
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
    </div>

    {{-- PHẦN MODAL (CHỈ HIỆN KHI $showModal = true) --}}
    @if($showModal)
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        @if($editingAttribute->exists)
                            Sửa thuộc tính: {{ $editingAttribute->name }}
                        @else
                            Tạo thuộc tính mới
                        @endif
                    </h5>
                    {{-- Nút X đóng modal --}}
                    <button type="button" class="close btn btn-link text-decoration-none text-dark" wire:click="closeModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
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

                        {{-- Logic hiển thị các trường phụ --}}
                        @if(isset($state['type']) && $state['type'] == 'number')
                        <div class="form-group mt-3" wire:key="unit-field">
                            <label>Đơn vị (Unit)</label>
                            <input type="text" 
                                   class="form-control @error('state.unit') is-invalid @enderror" 
                                   wire:model.defer="state.unit"
                                   placeholder="ví dụ: inch, kg, mAh...">
                            @error('state.unit') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        @endif

                        @if(isset($state['type']) && $state['type'] == 'select')
                        <div class="mt-3" wire:key="options-field">
                            <label>Quản lý Tùy chọn (Options)</label>
                            <div class="input-group mb-2">
                                <input type="text" 
                                       class="form-control @error('newOptionValue') is-invalid @enderror" 
                                       wire:model="newOptionValue" 
                                       wire:keydown.enter.prevent="addOption"
                                       placeholder="Nhập tùy chọn mới (vd: 8GB)...">
                                <button class="btn btn-primary" type="button" wire:click.prevent="addOption">
                                    Thêm
                                </button>
                            </div>
                            @error('newOptionValue') <span class="text-danger small d-block mb-2">{{ $message }}</span> @enderror

                            <ul class="list-group" style="max-height: 200px; overflow-y: auto;">
                                @forelse($options as $index => $option)
                                    <li class="list-group-item d-flex justify-content-between align-items-center py-1">
                                        {{ $option['value'] }}
                                        <button type="button" class="btn btn-sm text-danger" 
                                                wire:click.prevent="removeOption({{ $index }})">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </li>
                                @empty
                                    <li class="list-group-item text-muted small">Chưa có tùy chọn nào.</li>
                                @endforelse
                            </ul>
                        </div>
                        @endif

                        {{-- Footer của Modal --}}
                        <div class="modal-footer mt-4 px-0 pb-0">
                            <button type="button" class="btn btn-secondary" wire:click="closeModal">Hủy</button>
                            <button type="submit" class="btn btn-success">Lưu lại</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>