<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use Livewire\Component;
use App\Models\Notification;

class ProductApproval extends Component
{
    public $productId; // ID nhận từ view cha
    public $reasonModalOpen = false;
    public $actionType = 'reject';
    
    // Dùng biến tách lẻ cho xịn sò giống trang danh sách
    public $reasonType = ''; 
    public $reasonNote = '';

    // Bắt buộc phải có mount để nhận ID sản phẩm
    public function mount($productId)
    {
        $this->productId = $productId;
    }

    // ✅ Duyệt sản phẩm
    public function approve()
    {
        $product = Product::find($this->productId);
        if (!$product) return;

        $product->update(['status' => 'approved']);
        
        Notification::create([
            'user_id' => $product->seller_id,
            'type' => 'product_approved',
            'message' => "Sản phẩm '{$product->name}' đã được duyệt.",
            'is_read' => false,
        ]);

        return redirect()->route('admin.products.index')->with('success', "Đã duyệt sản phẩm #{$product->id}");
    }

    // ❌ Mở modal (Nhận type để biết là Reject hay Hidden)
    public function openRejectModal($type = 'reject')
    {
        $this->actionType = $type;
        $this->reasonType = ''; // Reset
        $this->reasonNote = ''; // Reset
        $this->reasonModalOpen = true;
    }

    // 🔒 Đóng modal
    public function closeModal()
    {
        $this->reasonModalOpen = false;
    }

    // ✅ Xác nhận từ chối
    public function confirmReject()
    {
        $this->validate([
            'reasonType' => 'required|string',
        ]);

        $product = Product::find($this->productId);
        if (!$product) return;

        // Xử lý status
        if ($this->actionType === 'hidden') {
            $product->update(['status' => 'hidden']);
            $msgType = 'ẩn';
        } else {
            $product->update(['status' => 'rejected']);
            $msgType = 'từ chối';
        }

        // Gộp lý do
        $fullReason = $this->reasonType;
        if (!empty($this->reasonNote)) {
            $fullReason .= " - " . $this->reasonNote;
        }

        Notification::create([
            'user_id' => $product->seller_id,
            'type' => "product_{$this->actionType}",
            'message' => "Sản phẩm '{$product->name}' bị {$msgType}. Lý do: {$fullReason}",
            'is_read' => false,
        ]);

        $this->closeModal();

        return redirect()->route('admin.products.index')
            ->with('success', "Đã {$msgType} sản phẩm #{$product->id}");
    }

    public function render()
    {
        $reasonOptions = [
            'Hình ảnh mờ, không rõ nét',
            'Sản phẩm vi phạm bản quyền',
            'Thông tin mô tả sai lệch',
            'Giá bán không hợp lý',
            'Sản phẩm cấm',
            'Spam/Trùng lặp',
        ];

        return view('livewire.admin.product-approval', compact('reasonOptions'));
    }
}