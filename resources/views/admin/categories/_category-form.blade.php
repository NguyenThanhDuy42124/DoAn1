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
            
            <div class="form-group mt-3">
                <label>Danh mục cha</label>
                <select class="form-control @error('state.parent_id') is-invalid @enderror" 
                        wire:model.defer="state.parent_id">
                    <option value="">— (Là danh mục gốc) —</option>
                    
                    @foreach($formattedCategories as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                @error('state.parent_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            
            <div class="form-group mt-3">
                <label>Mô tả</label>
                <textarea class="form-control" 
                          wire:model.defer="state.description"></textarea>
            </div>

            <hr>

            <h5>Gán Thuộc tính</h5>
            <div class="attribute-list" style="max-height: 200px; overflow-y: auto;">
                @foreach($allAttributes as $attribute)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" 
                               value="{{ $attribute->id }}" 
                               id="attr-{{ $attribute->id }}"
                               wire:model.defer="selectedAttributes"> <label class="form-check-label" for="attr-{{ $attribute->id }}">
                            {{ $attribute->name }} ({{ $attribute->type }})
                        </label>
                    </div>
                @endforeach
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