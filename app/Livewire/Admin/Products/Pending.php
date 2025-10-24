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

    public $search = '';
    public $selected = [];
    public $selectAll = false;
    public $reason = ''; // Cho reject

    protected $queryString = ['search'];
    public $selectedProductId;
    public $actionType = 'reject'; // reject | hidden
    public $reasonModalOpen = false;



    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = $this->getProductsQuery()->pluck('id')->toArray();
        } else {
            $this->selected = [];
        }
    }

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
        $this->resetPage();
    }

    public function reject($id)
    {
        $this->validate(['reason' => 'required|string|max:255']);

        $product = Product::findOrFail($id);
        $product->update(['status' => Product::STATUS_REJECTED]);

        Notification::create([
            'user_id' => $product->seller_id,
            'type' => 'product_rejected',
            'message' => "Sản phẩm '{$product->name}' bị từ chối: {$this->reason}",
            'is_read' => false,
        ]);

        $this->reason = '';
        Session::flash('success', 'Đã từ chối sản phẩm!');
        $this->resetPage();
    }

    public function bulkApprove()
    {
        if (empty($this->selected)) {
            Session::flash('error', 'Chọn ít nhất một sản phẩm!');
            return;
        }

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
        Session::flash('success', 'Đã duyệt hàng loạt!');
        $this->resetPage();
    }

    public function bulkReject()
    {
        $this->validate(['reason' => 'required|string|max:255']);

        if (empty($this->selected)) {
            Session::flash('error', 'Chọn ít nhất một sản phẩm!');
            return;
        }

        $products = Product::whereIn('id', $this->selected)->get();
        foreach ($products as $product) {
            $product->update(['status' => Product::STATUS_REJECTED]);
            Notification::create([
                'user_id' => $product->seller_id,
                'type' => 'product_rejected',
                'message' => "Sản phẩm '{$product->name}' bị từ chối (hàng loạt): {$this->reason}",
                'is_read' => false,
            ]);
        }

        $this->reason = '';
        $this->selected = [];
        $this->selectAll = false;
        Session::flash('success', 'Đã từ chối hàng loạt!');
        $this->resetPage();
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

        return view('admin.products.pending', compact('products'));
    }

    public function openRejectModal($id)
    {
        $this->selectedProductId = $id;
        $this->reasonModalOpen = true;
    }
    public function closeRejectModal()
    {
        $this->reasonModalOpen = false;
        $this->reason = '';
        $this->selectedProductId = null;
    }
    public function confirmReject()
    {
        $this->validate([
            'reason' => 'required|string|max:255',
            'actionType' => 'required|in:reject,hidden',
        ]);

        $product = Product::findOrFail($this->selectedProductId);

        if ($this->actionType === 'reject') {
            $product->update(['status' => Product::STATUS_REJECTED]);
        } else {
            $product->update(['status' => Product::STATUS_HIDDEN]);
        }

        Notification::create([
            'user_id' => $product->seller_id,
            'type' => 'product_' . $this->actionType,
            'message' => "Sản phẩm '{$product->name}' bị {$this->actionType}: {$this->reason}",
            'is_read' => false,
        ]);

        // Reset
        $this->reason = '';
        $this->reasonModalOpen = false;
        $this->selectedProductId = null;
        session()->flash('success', 'Đã xử lý sản phẩm!');
    }
}
