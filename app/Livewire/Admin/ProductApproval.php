<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use Livewire\Component;
use App\Models\Notification;

class ProductApproval extends Component
{
    public $reasonModalOpen = false;
    public $selectedProductId;
    public $actionType = 'reject';
    public $reason = '';
    public $productId;

    protected $rules = [
        'reason' => 'required|min:5',
        'actionType' => 'required|in:reject,hidden',
    ];

    // ✅ Duyệt sản phẩm
    public function approve($productId)
    {
        $product = Product::find($productId);
        if (!$product) return;

        $product->status = 'approved';
        $product->save();
        $message = "Sản phẩm #{$product->id} đã được duyệt.";

        return redirect()->route('admin.products.index')->with('message', $message);
    }

    // ❌ Mở modal từ chối
    public function openRejectModal($productId)
    {
        $this->selectedProductId = $productId;
        $this->reasonModalOpen = true;
    }

    // 🔒 Đóng modal
    public function closeRejectModal()
    {
        $this->reset(['reasonModalOpen', 'reason', 'actionType']);
    }

    // ✅ Xác nhận từ chối
    public function confirmReject()
    {
        $this->validate();

        $product = Product::find($this->selectedProductId);
        if (!$product) return;

        $product->status = $this->actionType === 'hidden' ? 'hidden' : 'rejected';
        $product->save();

        Notification::create([
            'user_id' => $product->seller_id,
            'type' => "product_{$this->actionType}",
            'message' => "Sản phẩm #{$product->id} bị {$this->actionType} vì: {$this->reason}",
        ]);

        $this->closeRejectModal();
        $message = $this->actionType === 'hidden' ? 'ẩn' : 'từ chối';

        return redirect()->route('admin.products.index')->with('error', "❌ Đã {$message} sản phẩm ID: {$this->selectedProductId}");
    }

    public function render()
    {
        return view('livewire.admin.product-approval');
    }
}
