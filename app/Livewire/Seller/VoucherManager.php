<?php

namespace App\Livewire\Seller;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Voucher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class VoucherManager extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    // Biến cho Modal
    public $showModal = false;
    public $isEditMode = false;
    public $voucherIdBeingEdited = null;

    // Các trường dữ liệu Voucher
    public $code, $name, $type = 'fixed', $value, $min_order_value = 0, $max_discount_amount;
    public $quantity = 100, $start_date, $expiry_date;
    public $is_active = true;
    public $canEditSensitiveData = true;

    // Reset form khi đóng modal
    public function resetForm()
    {
        $this->code = '';
        $this->name = '';
        $this->type = 'fixed';
        $this->value = '';
        $this->min_order_value = 0;
        $this->max_discount_amount = null;
        $this->quantity = 100;
        $this->start_date = null;
        $this->expiry_date = null;
        $this->is_active = true;
        $this->isEditMode = false;
        $this->voucherIdBeingEdited = null;
        $this->resetErrorBag();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->start_date = now()->format('Y-m-d\TH:i');
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $this->resetForm();
        $this->isEditMode = true;
        $this->voucherIdBeingEdited = $id;

        $voucher = Voucher::where('seller_id', Auth::id())->findOrFail($id);

        $this->canEditSensitiveData = ($voucher->used_count == 0);

        $this->code = $voucher->code;
        $this->name = $voucher->name;
        $this->type = $voucher->type;
        $this->value = 0 + $voucher->value; // Cần format nếu là float
        $this->min_order_value = 0 + $voucher->min_order_value;
        $this->max_discount_amount = $voucher->max_discount_amount;
        $this->quantity = $voucher->quantity;
        // Format date cho input datetime-local (Y-m-d\TH:i)
        $this->start_date = $voucher->start_date ? $voucher->start_date->format('Y-m-d\TH:i') : null;
        $this->expiry_date = $voucher->expiry_date ? $voucher->expiry_date->format('Y-m-d\TH:i') : null;
        $this->is_active = $voucher->is_active;

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    // Validate Rules
    protected function rules()
    {
        return [
            'code' => [
                'required', 'string', 'max:50',
                // Code phải unique trong bảng vouchers, trừ chính nó ra khi edit
                Rule::unique('vouchers', 'code')->ignore($this->voucherIdBeingEdited),
            ],
            'name' => 'required|string|max:255',
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric|min:1',
            'min_order_value' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'expiry_date' => 'nullable|date|after:start_date',
        ];
        if (!$this->isEditMode) {
            $rules['start_date'] = 'nullable|date|after_or_equal:' . now()->subMinute()->format('Y-m-d H:i');
        } else {
            // Nếu đang Edit -> Chỉ cần là ngày hợp lệ (vì voucher cũ có thể đã bắt đầu từ hôm qua)
            $rules['start_date'] = 'nullable|date';
        }
        return $rules;
    }

    public function save()
    {
        $this->validate();

        $startDate = $this->start_date ? $this->start_date : now();

        // Data chuẩn bị lưu
        $data = [
            // Những trường này luôn được phép sửa
            'seller_id' => Auth::id(),
            'name' => $this->name,
            'quantity' => (int)$this->quantity,
            'expiry_date' => $this->expiry_date,
            'is_active' => true,
        ];

        if (!$this->isEditMode || $this->canEditSensitiveData) {
            $data['code'] = strtoupper($this->code);
            $data['type'] = $this->type;
            $data['value'] = (float)$this->value;
            $data['min_order_value'] = (float)($this->min_order_value ?? 0);
            $data['max_discount_amount'] = $this->type === 'percent' ? (float)$this->max_discount_amount : null;
            $data['start_date'] = $startDate; // Ngày bắt đầu cũng không nên sửa nếu đã chạy
        }

        if ($this->isEditMode) {
            $voucher = Voucher::where('seller_id', Auth::id())->findOrFail($this->voucherIdBeingEdited);
            $voucher->update($data);
            session()->flash('success', 'Cập nhật voucher thành công!');
        } else {
            Voucher::create($data);
            session()->flash('success', 'Tạo voucher mới thành công!');
        }

        $this->closeModal();
        $this->resetForm();
    }

    // Toggle Active/Inactive
    public function toggleStatus($id)
    {
        $voucher = Voucher::where('seller_id', Auth::id())->findOrFail($id);
        $voucher->is_active = !$voucher->is_active;
        $voucher->save();
        
        // Dispatch event để hiển thị toast notification nếu muốn
        session()->flash('success', 'Đã thay đổi trạng thái voucher.');
    }

    public function delete($id)
    {
        $voucher = Voucher::where('seller_id', Auth::id())->findOrFail($id);
        
        if ($voucher->used_count > 0) {
            session()->flash('error', 'Không thể xóa voucher đã có người sử dụng. Hãy tắt nó đi.');
            return;
        }

        $voucher->delete();
        session()->flash('success', 'Đã xóa voucher.');
    }

    public function render()
    {
        $vouchers = Voucher::where('seller_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.seller.voucher-manager', [
            'vouchers' => $vouchers
        ])->layout('layouts.SellerDashBoard');
    }
}