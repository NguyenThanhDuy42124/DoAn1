<?php

namespace App\Livewire\Admin\Brands;

use Livewire\Component;
use App\Models\Brand; // <-- Đổi

class Manager extends Component
{
    // 1. Dùng để render cái bảng
    public $brands; // <-- Đổi

    // 2. Thuộc tính cho Form (Modal)
    public $showModal = false;
    public ?Brand $editingBrand; // <-- Đổi
    public $state = []; 

    /**
     * Khởi chạy component
     */
    public function mount()
    {
        $this->loadBrands(); // <-- Đổi
        $this->editingBrand = new Brand(); // <-- Đổi
    }

    /**
     * Lấy danh sách
     */
    public function loadBrands() // <-- Đổi
    {
        $this->brands = Brand::orderBy('name')->get(); // <-- Đổi
    }

    //--- PHẦN XỬ LÝ FORM ---

    /**
     * Mở modal để tạo mới
     */
    public function createNewBrand() // <-- Đổi
    {
        $this->resetErrorBag();
        $this->editingBrand = new Brand(); // <-- Đổi
        $this->state = []; 
        $this->showModal = true;
    }

    /**
     * Mở modal để sửa
     */
    public function editBrand($brandId) // <-- Đổi
    {
        $this->resetErrorBag();
        $this->editingBrand = Brand::find($brandId); // <-- Đổi
        $this->state = $this->editingBrand->toArray(); 
        $this->showModal = true;
    }

    /**
     * Lưu (Tạo mới hoặc Cập nhật)
     */
    public function saveBrand() // <-- Đổi
    {
        $rules = [
            'state.name' => 'required|string|max:255',
            // Xóa trường 'type'
        ];

        // Kiểm tra trùng lặp tên
        if (!$this->editingBrand->exists) {
            $rules['state.name'] .= '|unique:brands,name'; // <-- Đổi
        }

        $this->validate($rules);

        // 1. Lưu thông tin
        $this->editingBrand->fill($this->state);
        $this->editingBrand->save();

        // 2. Đóng modal và tải lại danh sách
        $this->showModal = false;
        $this->loadBrands(); // <-- Đổi
    }

    /**
     * Xóa
     */
    public function deleteBrand($brandId) // <-- Đổi
    {
        try {
            Brand::find($brandId)->delete(); // <-- Đổi
            $this->loadBrands(); // <-- Đổi
        } catch (\Exception $e) {
            // Xử lý lỗi
        }
    }

    public function render()
    {
        return view('admin.brands.manager') // <-- Đổi
               ->layout('layouts.AdminDashBoard');
    }
}