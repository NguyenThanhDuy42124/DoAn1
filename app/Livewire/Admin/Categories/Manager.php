<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use App\Models\Attribute; // Giữ lại
use App\Models\Brand; // <--- 1. THÊM MODEL BRAND
use Livewire\Component;

class Manager extends Component
{
    // 1. Thuộc tính cho Danh sách
    public $categories; // Collection của TẤT CẢ danh mục

    // 2. Thuộc tính cho Form (Modal)
    public $showModal = false;
    public ?Category $editingCategory; // Category đang được sửa
    public $state = []; // Dữ liệu form (wire:model="state.name")

    // 3. Thuộc tính cho việc Gán Thuộc tính (VẪN GIỮ NGUYÊN)
    public $allAttributes;
    public $selectedAttributes = []; // Mảng các ID thuộc tính được check

    // --- Brands (THÊM MỚI) ---
    public $allBrands; // <--- 2. Biến chứa tất cả Brand
    public $selectedBrands = []; // <--- 3. Biến chứa ID các Brand được chọn

    /**
     * Khởi chạy component
     */
    public function mount()
    {
        // Vẫn load "Khuôn Mẫu"
        $this->allAttributes = Attribute::orderBy('name')->get(); 
        $this->allBrands = Brand::orderBy('name')->get(); // <--- 4. Load tất cả Brand
        
        // Load danh sách đơn giản
        $this->loadCategories(); 

        // Khởi tạo model rỗng
        $this->editingCategory = new Category();
    }

    /**
     * Lấy dữ liệu danh sách (siêu đơn giản)
     */
    public function loadCategories()
    {
        // Không còn whereNull, không còn 'children', không còn 'sort_order'
        $this->categories = Category::orderBy('name')->get(); 
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
        $this->selectedBrands = []; // <--- 5. Reset brand khi tạo mới
        $this->showModal = true;
    }

    /**
     * Mở modal để sửa
     */
    public function editCategory($categoryId)
    {
        $this->resetErrorBag();
        
        // *** SỬA DÒNG NÀY: Thêm 'brands' vào with() ***
        $this->editingCategory = Category::with(['attributes', 'brands'])->find($categoryId);
        
        // Kiểm tra nếu không tìm thấy (để tránh lỗi crash)
        if (!$this->editingCategory) {
            $this->dispatch('error', 'Danh mục không tồn tại!');
            return;
        }
        
        // Nạp dữ liệu vào form
        $this->state = $this->editingCategory->only(['name']); 
        
        // Fill dữ liệu cho checkbox
        $this->selectedAttributes = $this->editingCategory->attributes->pluck('id')->toArray();
        
        // *** DÒNG NÀY SẼ CHẠY NGON NẾU MODEL ĐÃ CÓ QUAN HỆ brands() ***
        $this->selectedBrands = $this->editingCategory->brands->pluck('id')->toArray(); 
        
        $this->showModal = true;
    }

    /**
     * Lưu (Tạo mới hoặc Cập nhật)
     */
    public function saveCategory()
    {
        // Rule siêu đơn giản
        $rules = [
            'state.name' => 'required|string|max:255|unique:categories,name,' . $this->editingCategory->id,
        ];

        $this->validate($rules);

        // 1. Lưu thông tin cơ bản (chỉ có 'name')
        $this->editingCategory->fill($this->state);
        $this->editingCategory->save();

        // 2. Đồng bộ hóa (sync) các thuộc tính (VẪN GIỮ NGUYÊN)
        // Đây là logic "Khuôn Mẫu"
        $this->editingCategory->attributes()->sync($this->selectedAttributes);

        $this->editingCategory->brands()->sync($this->selectedBrands); // <--- 8. Lưu vào bảng pivot

        // 3. Đóng modal và tải lại danh sách
        $this->showModal = false;
        $this->loadCategories(); // Tải lại danh sách
        // Không cần redirect cả trang
    }

    //--- XÓA TOÀN BỘ PHẦN KÉO-THẢ VÀ ĐỆ QUY ---
    // Xóa hàm updateOrder()
    // Xóa hàm updateRecursive()
    // Xóa hàm loadFormattedCategories()
    // Xóa hàm buildCategoryList()

    //--- PHẦN RENDER ---
    public function render()
    {
        // Không cần gọi loadFormattedCategories() nữa
        return view('admin.categories.manager')->layout('layouts.AdminDashBoard');
    }
}