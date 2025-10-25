<div>
    <div class="row">
        <div class="col-md-7">
            <h3>Quản lý Danh mục</h3>
            <h2>{{ $testProperty }}</h2>
            <button class="btn btn-primary mb-3" wire:click="createNewCategory">
                Thêm danh mục mới
            </button>
            
            <div wire:ignore 
                 x-data="categoryTree()" 
                 x-init="initSortable($el)">
                
                <ol class="dd-list">
                    @include('admin.categories._category-tree-item', ['categories' => $categories])
                </ol>
            </div>
        </div>
        
        <div class="col-md-5">
            @if($showModal)
                @include('admin.categories._category-form')
            @endif
        </div>
    </div>
</div>

@push('scripts')
{{-- XÓA DÒNG NÀY: <script src="/path/to/jquery.nestable.min.js"></script> --}}

<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<script>
    // Hàm Alpine.js được định nghĩa ở đây
    function categoryTree() {
        return {
            initSortable(rootEl) {
                // Tìm tất cả các list con (kể cả list gốc)
                let lists = rootEl.querySelectorAll('ol');
                lists.forEach(list => {
                    Sortable.create(list, {
                        group: 'categories', // Cho phép kéo giữa các list
                        animation: 150,
                        
                        // Đây là phần quan trọng
                        onEnd: (evt) => {
                            // Lấy list gốc
                            let rootList = rootEl.querySelector('ol');
                            // Chuyển cây HTML thành JSON
                            let data = this.serialize(rootList);
                            
                            // Gửi JSON về Livewire.
                            @this.call('updateOrder', JSON.stringify(data));
                        }
                    });
                });
            },

            /**
             * Hàm này đọc cây HTML và tạo ra JSON
             * y hệt định dạng của Nestable2.
             */
            serialize(list) {
                let items = [];
                // Lặp qua từng <li> trong <ol>
                Array.from(list.children).forEach(li => {
                    // Chỉ lấy <li>, bỏ qua các thẻ rác nếu có
                    if (li.tagName !== 'LI') return;

                    let item = {
                        // Lấy id từ data-id
                        id: li.dataset.id 
                    };
                    
                    // Tìm <ol> con bên trong <li> này
                    let nestedList = li.querySelector('ol');
                    
                    // Nếu có <ol> con và nó có <li> bên trong
                    if (nestedList && nestedList.children.length > 0) {
                        // Chạy đệ quy
                        item.children = this.serialize(nestedList);
                    }
                    
                    items.push(item);
                });
                
                return items;
            }
        }
    }
</script>

{{-- XÓA HOÀN TOÀN CODE JQUERY CŨ CỦA NESTABLE --}}
@endpush