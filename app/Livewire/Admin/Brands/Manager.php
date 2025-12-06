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

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetErrorBag();
        $this->state = [];
        
        // FIX LỖI: Reset model về rỗng để tránh nhớ ID cũ
        $this->editingBrand = new Brand();
        $this->loadBrands();
    }

    public function createNewBrand()
    {
        $this->closeModal(); // Reset trước
        // Dòng dưới thực ra closeModal đã làm rồi, nhưng để lại cũng không sao
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
        // Fix lỗi unique: thêm id vào để tránh lỗi khi update chính nó
        $this->validate([
            'state.name' => 'required|string|max:255|unique:brands,name,' . ($this->editingBrand->id ?? ''),
        ]);

        // Lưu
        $this->editingBrand->fill($this->state);
        $this->editingBrand->save();

        // Xong phim -> gọi closeModal để reset sạch sẽ
        $this->closeModal();
    }

    public function deleteBrand($brandId)
    {
        try {
            $brand = Brand::find($brandId);
            if ($brand) {
                $brand->delete();
            }

            // FIX LỖI 404: Nếu xóa đúng thằng đang sửa thì phải reset ngay
            if ($this->editingBrand && $this->editingBrand->id == $brandId) {
                $this->editingBrand = new Brand();
            }

            $this->loadBrands();
        } catch (\Exception $e) {
            // Có thể thêm thông báo lỗi nếu cần
        }
    }

    public function render()
    {
        return view('admin.brands.manager')->layout('layouts.AdminDashBoard');
    }
}