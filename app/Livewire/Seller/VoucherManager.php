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
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $this->resetForm();
        $this->isEditMode = true;
        $this->voucherIdBeingEdited = $id;

        $voucher = Voucher::where('seller_id', Auth::id())->findOrFail($id);

        $this->code = $voucher->code;
        $this->name = $voucher->name;
        $this->type = $voucher->type;
        $this->value = $voucher->value; // Cần format nếu là float
        $this->min_order_value = $voucher->min_order_value;
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
            'start_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:start_date',
        ];
    }

    public function save()
    {
        $this->validate();

        $startDate = $this->start_date ? $this->start_date : now();

        // Data chuẩn bị lưu
        $data = [
            'seller_id' => Auth::id(),
            'code' => strtoupper($this->code),
            'name' => $this->name,
            'type' => $this->type,
            'value' => (float)$this->value,
            'min_order_value' => $this->min_order_value ?? 0,
            'max_discount_amount' => $this->type === 'percent' ? $this->max_discount_amount : null,
            'quantity' => (int)$this->quantity,
            'start_date' => $startDate,
            'expiry_date' => $this->expiry_date,
            'is_active' => true, // Mặc định tạo mới là active
        ];

        if ($this->isEditMode) {
            $voucher = Voucher::where('seller_id', Auth::id())->findOrFail($this->voucherIdBeingEdited);
            
            // Không cho sửa code nếu voucher đã có người dùng (optional logic)
            if($voucher->used_count > 0 && $voucher->code !== $data['code']) {
                $this->addError('code', 'Không thể đổi mã Voucher khi đã có người sử dụng.');
                return;
            }
            
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