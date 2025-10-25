@foreach($categories as $category)
    <li data-id="{{ $category->id }}">
        
        <div style="padding: 5px; border: 1px solid #ddd; margin-bottom: 5px; background: #f9f9f9; display: flex; justify-content: space-between; align-items: center;">
            
            <span>
                <i class="fas fa-arrows-alt" style="cursor: move; margin-right: 8px;"></i>
                {{ $category->name }}
            </span>
            
            <button class="btn btn-sm btn-info" 
                    @click="$wire.call('editCategory', {{ $category->id }})">
                Sửa
            </button>
        </div>

        @if($category->children->count() > 0)
            <ol style="padding-left: 30px;">
                @include('admin.categories._category-tree-item', ['categories' => $category->children])
            </ol>
        @endif
    </li>
@endforeach