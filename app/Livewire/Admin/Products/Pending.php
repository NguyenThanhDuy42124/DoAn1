<?php

namespace App\Livewire\Admin\Products;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Notification;
use Illuminate\Support\Facades\Session;

class Pending extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // --- BIẾN FILTER ---
    public $search = '';
    protected $queryString = ['search'];

    // --- BIẾN SELECTION ---
    public $selected = [];
    public $selectAll = false;

    // --- BIẾN MODAL & LÝ DO ---
    public $reasonType = ''; // Lý do chọn từ list
    public $reasonNote = ''; // Ghi chú thêm
    public $selectedProductId;
    public $actionType = 'reject'; // reject | hidden | approve
    public $reasonModalOpen = false;
    public $isBulk = false; // Đánh dấu xử lý hàng loạt

    // Reset phân trang khi search thay đổi (Lấy từ HEAD)
    public function updatedSearch() 
    { 
        $this->resetPage(); 
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = $this->getProductsQuery()->pluck('id')->toArray();
        } else {
            $this->selected = [];
        }
    }

    // --- DUYỆT NHANH (1 SẢN PHẨM) ---
    public function approve($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['status' => Product::STATUS_APPROVED]);

        Notification::create([
            'user_id' => $product->seller_id,
            'type' => 'product_approved',
            'message' => "Sản phẩm '{$product->name}' đã được duyệt.",
            'is_read' => false,
        ]);

        Session::flash('success', 'Đã duyệt sản phẩm!');
        // Không reset page để admin duyệt tiếp các sp khác cùng trang
    }

    // --- DUYỆT HÀNG LOẠT ---
    public function bulkApprove()
    {
        if (empty($this->selected)) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Chưa chọn sản phẩm nào!']);
            return;
        }

        // Lấy danh sách sản phẩm 1 lần thôi (Fix lỗi lặp query 2 lần của mày)
        $products = Product::whereIn('id', $this->selected)->get();
        
        foreach ($products as $product) {
            $product->update(['status' => Product::STATUS_APPROVED]);
            
            Notification::create([
                'user_id' => $product->seller_id,
                'type' => 'product_approved',
                'message' => "Sản phẩm '{$product->name}' đã được duyệt (hàng loạt).",
                'is_read' => false,
            ]);
        }

        $this->selected = [];
        $this->selectAll = false;
        Session::flash('success', 'Đã duyệt hàng loạt ' . count($products) . ' sản phẩm!');
    }

    // --- MỞ MODAL CHO 1 SẢN PHẨM ---
    public function openRejectModal($id, $type = 'reject')
    {
        $this->selectedProductId = $id;
        $this->actionType = $type;
        $this->isBulk = false; // Xử lý lẻ
        
        // Reset form
        $this->reasonType = ''; 
        $this->reasonNote = '';
        $this->reasonModalOpen = true;
    }

    // --- MỞ MODAL HÀNG LOẠT ---
    public function openBulkRejectModal()
    {
        if (empty($this->selected)) {
             session()->flash('error', 'Vui lòng chọn ít nhất 1 sản phẩm!');
             return;
        }

        $this->isBulk = true; // Xử lý hàng loạt
        $this->actionType = 'reject';
        
        $this->reasonType = ''; 
        $this->reasonNote = '';
        $this->reasonModalOpen = true;
    }

    public function closeRejectModal()
    {
        $this->reasonModalOpen = false;
        $this->reasonType = '';
        $this->reasonNote = '';
        $this->selectedProductId = null;
        $this->isBulk = false;
    }

    // --- XÁC NHẬN (XỬ LÝ CHUNG CHO CẢ LẺ VÀ BULK) ---
    public function confirmReject()
    {
        // 1. Validate
        if ($this->actionType !== 'approve') {
             $this->validate([
                'reasonType' => 'required|string',
            ]);
        }

        // 2. Chuẩn bị lý do
        $fullReason = $this->reasonType;
        if (!empty($this->reasonNote)) {
            $fullReason .= " - " . $this->reasonNote;
        }

        // --- TRƯỜNG HỢP 1: XỬ LÝ HÀNG LOẠT ---
        if ($this->isBulk) {
            $products = Product::whereIn('id', $this->selected)->get();
            
            foreach ($products as $product) {
                $product->update(['status' => Product::STATUS_REJECTED]);
                
                Notification::create([
                    'user_id' => $product->seller_id,
                    'type' => 'product_rejected',
                    'message' => "Sản phẩm '{$product->name}' bị từ chối. Lý do: {$fullReason}",
                    'is_read' => false,
                ]);
            }
            
            $this->selected = [];
            $this->selectAll = false;
            session()->flash('success', 'Đã từ chối ' . count($products) . ' sản phẩm!');

        } 
        // --- TRƯỜNG HỢP 2: XỬ LÝ LẺ ---
        else {
            $product = Product::findOrFail($this->selectedProductId);
            $msg = '';

            if ($this->actionType === 'reject') {
                $product->update(['status' => Product::STATUS_REJECTED]);
                $msg = "bị từ chối";
            } elseif ($this->actionType === 'hidden') {
                $product->update(['status' => Product::STATUS_HIDDEN]);
                $msg = "bị ẩn";
            } else {
                $product->update(['status' => Product::STATUS_APPROVED]);
                $msg = "được duyệt";
                $fullReason = "Sản phẩm hợp lệ";
            }

            Notification::create([
                'user_id' => $product->seller_id,
                'type' => 'product_' . $this->actionType,
                'message' => "Sản phẩm '{$product->name}' {$msg}. Lý do: {$fullReason}",
                'is_read' => false,
            ]);
            
            session()->flash('success', 'Đã xử lý sản phẩm!');
        }

        $this->closeRejectModal();
    }

    protected function getProductsQuery()
    {
        return Product::pending()
            ->with(['seller', 'images', 'category'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%")
                    ->orWhereHas('seller', function ($q) {
                        $q->where('name', 'like', "%{$this->search}%");
                    });
            })
            ->latest();
    }

    public function render()
    {
        $products = $this->getProductsQuery()->paginate(10);

        // List lý do mẫu
        $reasonOptions = [
            'Hình ảnh mờ, không rõ nét',
            'Sản phẩm vi phạm bản quyền',
            'Thông tin mô tả sai lệch/thiếu',
            'Giá bán không hợp lý (quá cao/thấp)',
            'Sản phẩm thuộc danh mục cấm',
            'Spam/Đăng trùng lặp',
        ];

        return view('admin.products.pending', compact('products', 'reasonOptions'));
    }
}