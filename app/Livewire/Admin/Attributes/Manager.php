<?php

namespace App\Livewire\Admin\Attributes;

use Livewire\Component;
use App\Models\Attribute;
use App\Models\AttributeOption;
use Illuminate\Support\Facades\DB; // <-- Thêm

class Manager extends Component
{
    public $allAttributes;
    public $showModal = false;
    public ?Attribute $editingAttribute; 
    public $state = []; 

    // *** THÊM 2 DÒNG NÀY ***
    public $options = []; // Mảng quản lý các tùy chọn (cho type 'select')
    public $newOptionValue = ''; // Biến cho ô input "Thêm tùy chọn mới"

    public function mount()
    {
        $this->loadAttributes();
        $this->editingAttribute = new Attribute(); 
        $this->state = ['type' => 'text']; // Mặc định là 'text'
    }

    public function loadAttributes()
    {
        $this->allAttributes = Attribute::orderBy('name')->get();
    }

    //--- PHẦN XỬ LÝ FORM ---

    public function createNewAttribute()
    {
        $this->resetErrorBag();
        $this->editingAttribute = new Attribute(); 
        $this->state = ['type' => 'text']; // Mặc định
        $this->options = []; // Reset mảng
        $this->newOptionValue = '';
        $this->showModal = true;
    }

    public function editAttribute($attributeId)
    {
        $this->resetErrorBag();
        // Load kèm quan hệ 'options'
        $this->editingAttribute = Attribute::with('options')->find($attributeId); 
        
        $this->state = $this->editingAttribute->toArray(); 
        // Nạp các options vào mảng
        $this->options = $this->editingAttribute->options->toArray(); 
        $this->newOptionValue = '';
        
        $this->showModal = true;
    }

    // *** THÊM 2 HÀM MỚI ĐỂ QUẢN LÝ OPTIONS ***
    public function addOption()
    {
        if (empty(trim($this->newOptionValue))) {
            return;
        }
        // Thêm option mới vào mảng (chưa lưu DB)
        $this->options[] = [
            'id' => null, 
            'value' => trim($this->newOptionValue)
        ];
        $this->newOptionValue = ''; // Reset ô input
    }

    public function removeOption($index)
    {
        // Xóa option khỏi mảng (chưa xóa DB)
        unset($this->options[$index]);
        $this->options = array_values($this->options); // Sắp xếp lại index
    }

    /**
     * CẬP NHẬT HÀM LƯU (Quan trọng)
     */
    public function saveAttribute()
    {
        $rules = [
            'state.name' => 'required|string|max:255',
            'state.type' => 'required|string|in:text,select,number', 
            'state.unit' => 'nullable|string|max:50', // Rule cho 'unit'
        ];
        
        // Rule: Nếu type là 'select', mảng options không được rỗng
        if ($this->state['type'] == 'select' && empty($this->options)) {
            $this->addError('newOptionValue', 'Bạn phải thêm ít nhất một tùy chọn.');
            return;
        }

        $this->validate($rules);

        // Dùng transaction vì ta sửa 2 bảng
        DB::transaction(function () {
            
            // 1. Nếu type không phải 'number', xóa 'unit' đi
            if ($this->state['type'] != 'number') {
                $this->state['unit'] = null;
            }
            
            // 2. Lưu thuộc tính chính (bảng 'attributes')
            $this->editingAttribute->fill($this->state);
            $this->editingAttribute->save();

            // 3. Xử lý Options (bảng 'attribute_options')
            if ($this->state['type'] == 'select') {
                // Lấy ID của các options còn lại trong mảng $this->options
                $existingOptionIds = array_filter(array_column($this->options, 'id'));
                
                // Xóa các options đã bị xóa (những cái không còn trong mảng)
                AttributeOption::where('attribute_id', $this->editingAttribute->id)
                               ->whereNotIn('id', $existingOptionIds)
                               ->delete();
                
                // Cập nhật/Tạo mới các options
                foreach ($this->options as $index => $optionData) {
                    AttributeOption::updateOrCreate(
                        [
                            'id' => $optionData['id'] // Tìm bằng ID (nếu có)
                        ],
                        [
                            'attribute_id' => $this->editingAttribute->id,
                            'value' => $optionData['value'],
                            'sort_order' => $index + 1
                        ]
                    );
                }
            } else {
                // Nếu type không phải là 'select' (text, number), 
                // XÓA HẾT options của nó đi (phòng trường hợp đổi type)
                AttributeOption::where('attribute_id', $this->editingAttribute->id)->delete();
            }
        });

        // 4. Đóng modal và tải lại danh sách
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