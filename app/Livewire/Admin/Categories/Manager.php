<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use App\Models\Attribute;
use App\Models\Brand;
use Livewire\Component;

class Manager extends Component
{
    // Danh sách
    public $categories;

    // Modal & Form
    public $showModal = false;
    public ?Category $editingCategory;
    public $state = []; // Chứa name, slug...

    // Quan hệ
    public $allAttributes;
    public $selectedAttributes = [];

    public $allBrands;
    public $selectedBrands = [];

    public function mount()
    {
        $this->allAttributes = Attribute::orderBy('name')->get(); 
        $this->allBrands = Brand::orderBy('name')->get();
        $this->loadCategories(); 
        $this->editingCategory = new Category();
    }

    public function loadCategories()
    {
        // Load kèm đếm số lượng cho nhẹ query
        $this->categories = Category::withCount(['attributes', 'brands'])->orderBy('name')->get(); 
    }

    // --- XỬ LÝ FORM ---

    // HÀM MỚI: Đóng modal và reset sạch sẽ
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetErrorBag();
        $this->state = [];
        $this->selectedAttributes = [];
        $this->selectedBrands = [];
    }

    public function createNewCategory()
    {
        $this->closeModal(); // Reset trước cho chắc
        $this->editingCategory = new Category();
        $this->showModal = true; // Bật modal
    }

    public function editCategory($categoryId)
    {
        $this->resetErrorBag();
        
        $this->editingCategory = Category::with(['attributes', 'brands'])->find($categoryId);
        
        if (!$this->editingCategory) {
            return;
        }
        
        $this->state = $this->editingCategory->only(['name']); 
        $this->selectedAttributes = $this->editingCategory->attributes->pluck('id')->toArray();
        $this->selectedBrands = $this->editingCategory->brands->pluck('id')->toArray(); 
        
        $this->showModal = true;
    }

    public function saveCategory()
    {
        $this->validate([
            'state.name' => 'required|string|max:255|unique:categories,name,' . $this->editingCategory->id,
        ]);

        // 1. Lưu Category
        $this->editingCategory->fill($this->state);
        $this->editingCategory->save();

        // 2. Lưu quan hệ (Pivot table)
        $this->editingCategory->attributes()->sync($this->selectedAttributes);
        $this->editingCategory->brands()->sync($this->selectedBrands);

        // 3. Xong việc
        $this->closeModal();
        $this->loadCategories(); 
    }
    
    public function deleteCategory($id) {
        Category::find($id)->delete();
        $this->loadCategories();
    }

    public function render()
    {
        return view('admin.categories.manager')->layout('layouts.AdminDashBoard');
    }
}