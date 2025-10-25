<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use App\Models\Brand;
use App\Models\Attribute;
use Livewire\Component;


class Manager extends Component
{
    public $testProperty = 'HELLO WORLD';
    // 1. Thuộc tính cho Tree View
    public $categories; // Collection của các danh mục gốc (parent_id = null)

    // 2. Thuộc tính cho Form (Modal)
    public $showModal = false;
    public ?Category $editingCategory; // Category đang được sửa
    public $state = []; // Dữ liệu form (wire:model="state.name")
    
    // 3. Thuộc tính cho việc Gán Thuộc tính
    public $allAttributes;
    public $selectedAttributes = []; // Mảng các ID thuộc tính được check


    public $formattedCategories = [];

    /**
     * Khởi chạy component
     */
    public function mount()
    {
        $this->allAttributes = Attribute::orderBy('name')->get();
        $this->loadCategories();

        $this->editingCategory = new Category();
    }

    /**
     * Lấy dữ liệu cây (chỉ lấy cấp cao nhất,
     * quan hệ "children" sẽ tự động tải lồng nhau)
     */
    public function loadCategories()
    {
        $this->categories = Category::with('children') // Tải sẵn cấp con
                                    ->whereNull('parent_id')
                                    ->orderBy('sort_order')
                                    ->get();
    }

    //--- PHẦN 2: XỬ LÝ FORM ---

    /**
     * Mở modal để tạo mới
     */
    public function createNewCategory()
    {
        $this->resetErrorBag();
        $this->editingCategory = new Category(); // Model rỗng
        $this->state = []; // Xóa dữ liệu form
        $this->selectedAttributes = []; // Xóa thuộc tính đã chọn
        $this->showModal = true;
    }

    /**
     * Mở modal để sửa
     */
    public function editCategory($categoryId)
    {
        $this->resetErrorBag();
        $this->editingCategory = Category::with('attributes')->find($categoryId);
        
        // Nạp dữ liệu vào form
        $this->state = $this->editingCategory->toArray(); 
        
        // Nạp các thuộc tính đã được gán
        $this->selectedAttributes = $this->editingCategory->attributes->pluck('id')->toArray();
        
        $this->showModal = true;
    }

    /**
     * Lưu (Tạo mới hoặc Cập nhật)
     */
    public function saveCategory()
    {
        $rules = [
            'state.name' => 'required|string|max:255',
            'state.parent_id' => 'nullable|exists:categories,id',
            // Thêm các rules khác nếu cần
        ];
        
        // Chặn không cho chọn cha là chính nó
        if ($this->editingCategory->id) {
             $rules['state.parent_id'] .= '|not_in:' . $this->editingCategory->id;
        }

        $this->validate($rules);

        // 1. Lưu thông tin cơ bản
        $this->editingCategory->fill($this->state);
        $this->editingCategory->save();

        // 2. Đồng bộ hóa (sync) các thuộc tính (đây là mấu chốt)
        // sync() sẽ tự động thêm/xóa trong bảng category_attribute
        $this->editingCategory->attributes()->sync($this->selectedAttributes);

        // 3. Đóng modal và tải lại cây
        $this->showModal = false;
        
        // Dùng redirect để làm mới toàn bộ trang -> đảm bảo cây JS được
        // cập nhật 100% chính xác sau khi sửa/thêm.
        return redirect(request()->header('Referer'));
    }

    //--- PHẦN 1: XỬ LÝ KÉO-THẢ (DRAG & DROP) ---

    /**
     * Phương thức này được JS gọi khi người dùng thả chuột
     * $data là một JSON string từ Nestable
     * [ { "id": 1, "children": [ { "id": 2 } ] }, { "id": 3 } ]
     */
    public function updateOrder($data)
    {
        // Chuyển JSON thành mảng PHP
        $tree = json_decode($data, true); 

        $this->updateRecursive($tree);
        
        // Không cần loadCategories() vì JS đã cập nhật UI
        // và DB đã được cập nhật ở backend.
    }

    /**
     * Hàm đệ quy để cập nhật CSDL từ cây
     */
    private function updateRecursive($categories, $parentId = null)
    {
        foreach ($categories as $index => $categoryData) {
            // Cập nhật CSDL
            Category::where('id', $categoryData['id'])->update([
                'parent_id' => $parentId,
                'sort_order' => $index + 1 // $index bắt đầu từ 0
            ]);

            // Nếu có con, lặp lại
            if (isset($categoryData['children']) && count($categoryData['children']) > 0) {
                $this->updateRecursive($categoryData['children'], $categoryData['id']);
            }
        }
    }

    //--- PHẦN RENDER ---

    /**
     * Helper cho dropdown "Chọn danh mục cha"
     * Tạo ra một danh sách phẳng có định dạng
     * [ 1 => "Quần Áo", 3 => "-- Áo Sơ Mi" ]
     */
    public function loadFormattedCategories()
    {
        // 1. Lấy TẤT CẢ danh mục 1 lần duy nhất
        $allCategories = Category::orderBy('sort_order')->get();
        
        // 2. Nhóm chúng lại theo parent_id để dễ dàng xây dựng cây
        $groupedCategories = $allCategories->groupBy('parent_id');

        // 3. Lấy ID của danh mục đang sửa (nếu có)
        // Dùng optional() để an toàn nếu $editingCategory là null
        $editingId = optional($this->editingCategory)->id;

        $formatted = [];
        
        // 4. Bắt đầu xây dựng cây từ cấp gốc (parent_id = null)
        $this->buildCategoryList(
            $groupedCategories, 
            $formatted, 
            null, // Bắt đầu từ parent_id = null
            '',   // Không có prefix
            $editingId // ID cần loại trừ
        );
        
        $this->formattedCategories = $formatted;
    }

    private function buildCategoryList($groupedCategories, &$formatted, $parentId, $prefix = '', $excludeId = null)
    {
        // Lấy danh sách con từ collection đã nhóm, không query DB
        $children = $groupedCategories->get($parentId, collect());

        foreach ($children as $category) {
            // Nếu category này là category đang sửa (excludeId),
            // thì bỏ qua nó (và tất cả con của nó)
            if ($category->id == $excludeId) {
                continue; 
            }

            // Thêm vào danh sách
            $formatted[$category->id] = $prefix . ' ' . $category->name;

            // Kiểm tra xem nó có con không
            if ($groupedCategories->has($category->id)) {
                // Đệ quy để xử lý các con
                $this->buildCategoryList(
                    $groupedCategories, 
                    $formatted, 
                    $category->id, // parent_id mới là ID của category này
                    $prefix . '—', // Thêm gạch
                    $excludeId
                );
            }
        }
    }

    


    public function render()
    {
        $this->loadFormattedCategories();
        return view('admin.categories.manager')->layout('layouts.AdminDashBoard');
    }
}