<?php

namespace App\Livewire\Admin\Attributes;

use Livewire\Component;
use App\Models\Attribute;

class Manager extends Component
{
    // 1. Dùng để render cái bảng
    public $allAttributes;

    // 2. Thuộc tính cho Form (Modal)
    public $showModal = false;
    public ?Attribute $editingAttribute; // Thuộc tính đang được sửa
    public $state = []; // Dữ liệu form (wire:model="state.name")

    /**
     * Khởi chạy component
     */
    public function mount()
    {
        $this->loadAttributes();
        $this->editingAttribute = new Attribute(); // Khởi tạo rỗng
    }

    /**
     * Lấy danh sách thuộc tính
     */
    public function loadAttributes()
    {
        // Lấy tất cả, sắp xếp theo tên
        $this->allAttributes = Attribute::orderBy('name')->get();
    }

    //--- PHẦN XỬ LÝ FORM ---

    /**
     * Mở modal để tạo mới
     */
    public function createNewAttribute()
    {
        $this->resetErrorBag();
        $this->editingAttribute = new Attribute(); // Model rỗng
        $this->state = []; // Xóa dữ liệu form
        $this->showModal = true;
    }

    /**
     * Mở modal để sửa
     */
    public function editAttribute($attributeId)
    {
        $this->resetErrorBag();
        $this->editingAttribute = Attribute::find($attributeId);
        
        // Nạp dữ liệu vào form
        $this->state = $this->editingAttribute->toArray(); 
        
        $this->showModal = true;
    }

    /**
     * Lưu (Tạo mới hoặc Cập nhật)
     */
    public function saveAttribute()
    {
        $rules = [
            'state.name' => 'required|string|max:255',
            // Bắt buộc phải là 1 trong 3 loại này
            'state.type' => 'required|string|in:text,select,number', 
        ];

        // Kiểm tra trùng lặp tên
        if (!$this->editingAttribute->exists) {
            $rules['state.name'] .= '|unique:attributes,name';
        }

        $this->validate($rules);

        // 1. Lưu thông tin
        $this->editingAttribute->fill($this->state);
        $this->editingAttribute->save();

        // 2. Đóng modal và tải lại danh sách
        $this->showModal = false;
        $this->loadAttributes(); 
    }

    /**
     * Xóa thuộc tính
     */
    public function deleteAttribute($attributeId)
    {
        try {
            Attribute::find($attributeId)->delete();
            $this->loadAttributes();
        } catch (\Exception $e) {
            // Xử lý lỗi nếu nó bị khóa ngoại ràng buộc
            // $dispatch('show-error', 'Không thể xóa thuộc tính này...')
        }
    }

    public function render()
    {
        return view('admin.attributes.manager')
               ->layout('layouts.AdminDashBoard');
    }
}