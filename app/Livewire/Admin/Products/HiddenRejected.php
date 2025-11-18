<?php

namespace App\Livewire\Admin\Products;

use App\Models\Product;
use App\Models\ModerationLog;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class HiddenRejected extends Component
{
    use WithPagination;

    public $tab = 'hidden'; // hidden | rejected
    public $search = '';
    public $reason_filter = '';
    public $expanded = [];

    protected $queryString = ['tab', 'search', 'reason_filter'];

    public function mount()
    {
        $this->tab = request('tab', 'hidden');
    }

    public function switchTab($tab)
    {
        $this->tab = $tab;
        $this->resetPage();
        $this->expanded = [];
    }

    public function toggleExpand($id)
    {
        $this->expanded = $this->expanded[0] == $id ? [] : [$id];
    }

    public function restore($id)
    {
        $product = Product::findOrFail($id);
        $oldStatus = $product->status;
        $product->update(['status' => 'approved']);

        ModerationLog::create([
            'product_id' => $product->id,
            'admin_id' => Auth::id(),
            'action' => 'restore',
            'from_status' => $oldStatus,
            'to_status' => 'approved',
            'note' => 'Khôi phục từ ' . $oldStatus,
        ]);

        session()->flash('success', "Đã khôi phục sản phẩm #{$id}");
        $this->resetPage();
    }

    protected function getProductsQuery()
    {
        $status = $this->tab === 'hidden' ? 'hidden' : 'rejected';

        return Product::query()
            ->with(['seller'])
            ->where('status', $status)
            ->when($this->search, fn($q) => $q
                ->where('name', 'like', "%{$this->search}%")
                ->orWhereHas('seller', fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            )
            ->when($this->reason_filter, fn($q) => $q->whereHas('notifications', fn($q) => $q->where('message', 'like', "%{$this->reason_filter}%")))
            ->latest('updated_at');
    }

    public function render()
    {
        $products = $this->getProductsQuery()->paginate(15);

        $reasonOptions = [
            'Hình ảnh không rõ nét',
            'Vi phạm bản quyền',
            'Hàng giả, hàng nhái',
            'Nội dung không phù hợp',
            'Giá sai quy định',
            'Thông tin sai lệch',
        ];

        $selectedProduct = null;
        if (!empty($this->expanded)) {
            $selectedProduct = Product::with(['notifications' => fn($q) => $q->whereIn('type', ['product_hidden', 'product_rejected'])])
                ->find($this->expanded[0]);
        }

        return view('admin.products.hidden-rejected', [
            'products' => $products,
            'reasonOptions' => $reasonOptions,
            'selectedProduct' => $selectedProduct,
        ]);
    }
}