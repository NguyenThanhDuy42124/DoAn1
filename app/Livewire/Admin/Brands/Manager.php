<?php

namespace App\Livewire\Admin\Brands;

use Livewire\Component;
use App\Models\Brand;

class Manager extends Component
{
    // Danh sách brands
    public $brands;

    // Modal & Form
    public $showModal = false;
    public ?Brand $editingBrand;
    public $state = []; 

    public function mount()
    {
        $this->loadBrands();
        $this->editingBrand = new Brand();
    }

    public function loadBrands()
    {
        $this->brands = Brand::orderBy('name')->get();
    }

    // --- XỬ LÝ FORM ---

    // HÀM QUAN TRỌNG: Đóng modal và reset
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetErrorBag();
        $this->state = [];
    }

    public function createNewBrand()
    {
        $this->closeModal(); // Reset trước
        $this->editingBrand = new Brand();
        $this->showModal = true;
    }

    public function editBrand($brandId)
    {
        $this->resetErrorBag();
        $this->editingBrand = Brand::find($brandId);
        
        if ($this->editingBrand) {
            $this->state = $this->editingBrand->toArray();
            $this->showModal = true;
        }
    }

    public function saveBrand()
    {
        // Validate gọn: Bắt buộc, tối đa 255 ký tự, không trùng tên (trừ chính nó ra khi sửa)
        $this->validate([
            'state.name' => 'required|string|max:255|unique:brands,name,' . $this->editingBrand->id,
        ]);

        // Lưu
        $this->editingBrand->fill($this->state);
        $this->editingBrand->save();

        // Xong phim
        $this->closeModal();
        $this->loadBrands();
    }

    public function deleteBrand($brandId)
    {
        try {
            Brand::find($brandId)->delete();
            $this->loadBrands();
        } catch (\Exception $e) {
            // Có thể thêm thông báo lỗi nếu cần (vd: đang có sản phẩm dùng brand này)
        }
    }

    public function render()
    {
        return view('admin.brands.manager')->layout('layouts.AdminDashBoard');
    }
}