<?php

namespace App\Livewire\Admin\Attributes;

use Livewire\Component;
use App\Models\Attribute;
use App\Models\AttributeOption;
use Illuminate\Support\Facades\DB;

class Manager extends Component
{
    public $allAttributes;
    public $showModal = false;
    public ?Attribute $editingAttribute; 
    public $state = []; 

    public $options = []; 
    public $newOptionValue = ''; 

    public function mount()
    {
        $this->loadAttributes();
        $this->editingAttribute = new Attribute(); 
        $this->state = ['type' => 'text']; 
    }

    public function loadAttributes()
    {
        $this->allAttributes = Attribute::orderBy('name')->get();
    }

    // Reset các biến khi mở modal tạo mới
    public function createNewAttribute()
    {
        $this->resetValidation(); // Xóa các thông báo lỗi cũ
        $this->editingAttribute = new Attribute(); 
        $this->state = ['type' => 'text']; 
        $this->options = []; 
        $this->newOptionValue = '';
        $this->showModal = true;
    }

    // Load dữ liệu khi sửa
    public function editAttribute($attributeId)
    {
        $this->resetValidation();
        $this->editingAttribute = Attribute::with('options')->find($attributeId); 
        
        $this->state = $this->editingAttribute->toArray(); 
        $this->options = $this->editingAttribute->options->toArray(); 
        $this->newOptionValue = '';
        
        $this->showModal = true;
    }

    public function addOption()
    {
        if (empty(trim($this->newOptionValue))) {
            return;
        }
        $this->options[] = [
            'id' => null, 
            'value' => trim($this->newOptionValue)
        ];
        $this->newOptionValue = ''; 
    }

    public function removeOption($index)
    {
        unset($this->options[$index]);
        $this->options = array_values($this->options); 
    }

    public function saveAttribute()
    {
        $rules = [
            'state.name' => 'required|string|max:255',
            'state.type' => 'required|string|in:text,select,number', 
            'state.unit' => 'nullable|string|max:50', 
        ];
        
        if ($this->state['type'] == 'select' && empty($this->options)) {
            $this->addError('newOptionValue', 'Bạn phải thêm ít nhất một tùy chọn.');
            return;
        }

        $this->validate($rules);

        DB::transaction(function () {
            if ($this->state['type'] != 'number') {
                $this->state['unit'] = null;
            }
            
            $this->editingAttribute->fill($this->state);
            $this->editingAttribute->save();

            if ($this->state['type'] == 'select') {
                $existingOptionIds = array_filter(array_column($this->options, 'id'));
                
                AttributeOption::where('attribute_id', $this->editingAttribute->id)
                               ->whereNotIn('id', $existingOptionIds)
                               ->delete();
                
                foreach ($this->options as $index => $optionData) {
                    AttributeOption::updateOrCreate(
                        ['id' => $optionData['id']],
                        [
                            'attribute_id' => $this->editingAttribute->id,
                            'value' => $optionData['value'],
                            'sort_order' => $index + 1
                        ]
                    );
                }
            } else {
                AttributeOption::where('attribute_id', $this->editingAttribute->id)->delete();
            }
        });

        $this->showModal = false; // Đóng modal
        $this->loadAttributes(); 
    }

    public function deleteAttribute($attributeId)
    {
        // Thêm try-catch để an toàn
        try {
            Attribute::find($attributeId)->delete();
            $this->loadAttributes();
        } catch (\Exception $e) {
             // Có thể dispatch browser event báo lỗi nếu muốn
        }
    }

    public function render()
    {
        return view('admin.attributes.manager')
               ->layout('layouts.AdminDashBoard');
    }
}