<?php

namespace App\Livewire\Admin\Products;

use App\Models\User;
use App\Models\Product;
use Livewire\Component;
use App\Models\Category;
use Illuminate\Support\Str;
use App\Models\Notification;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Storage;
use Rappasoft\LaravelLivewireTables\DataTableComponent;

class All extends Component
{
    use WithPagination;

    // Search & Filters
    public $search = '';
    public $status = '';
    public $category_id = '';
    public $seller_id = '';
    public $price_min = '';
    public $price_max = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $selectedProductId;
    public $actionType = 'reject'; // reject | hidden
    public $reasonModalOpen = false;
    public $reason = ''; // Cho reject

    protected $queryString = [
        'search', 'status', 'category_id', 'seller_id',
        'price_min', 'price_max', 'sortField', 'sortDirection'
    ];

    public function updating($field)
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function export()
    {
        $products = $this->getProductsQuery()->get();

        return (new FastExcel($products))->download('all-products-' . now()->format('Y-m-d') . '.xlsx', function ($product) {
            return [
                'ID' => $product->id,
                'Tên sản phẩm' => $product->name,
                'Giá' => $product->price,
                'Trạng thái' => ucfirst($product->status),
                'Danh mục' => $product->category?->name,
                'Người bán' => $product->seller?->name,
                'Ngày tạo' => $product->created_at->format('d/m/Y'),
            ];
        });
    }

    protected function getProductsQuery()
    {
        return Product::query()
            ->with(['seller', 'category'])
            ->when($this->search, function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%")
                  ->orWhereHas('seller', fn($q) => $q->where('name', 'like', "%{$this->search}%"));
            })
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->category_id, fn($q) => $q->where('category_id', $this->category_id))
            ->when($this->seller_id, fn($q) => $q->where('seller_id', $this->seller_id))
            ->when($this->price_min !== '', fn($q) => $q->where('price', '>=', $this->price_min))
            ->when($this->price_max !== '', fn($q) => $q->where('price', '<=', $this->price_max))
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function render()
    {
        $products = $this->getProductsQuery()->paginate(20);

        // Stats
        $stats = [
            'total' => Product::count(),
            'approved' => Product::where('status', 'approved')->count(),
            'pending' => Product::where('status', 'pending')->count(),
            'rejected' => Product::where('status', 'rejected')->count(),
        ];
        $stats['approved_percent'] = $stats['total'] > 0 ? round(($stats['approved'] / $stats['total']) * 100, 1) : 0;

        // Top Categories (Pie Chart)
        $topCategories = Category::withCount('products')
            ->orderBy('products_count', 'desc')
            ->limit(6)
            ->get();

        $chartData = $topCategories->map(function ($cat) {
            return [
                'label' => $cat->name,
                'value' => $cat->products_count,
                'color' => '#' . substr(md5($cat->id), 0, 6),
            ];
        })->toJson();

        return view('admin.products.all', [
            'products' => $products,
            'categories' => Category::orderBy('name')->get(),
            'users' => User::where('role', 'seller')->orderBy('name')->get(),
            'stats' => $stats,
            'chartData' => $chartData,
        ]);
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
            'actionType' => 'required|in:reject,hidden,approve',
        ]);

        $product = Product::findOrFail($this->selectedProductId);

        if ($this->actionType === 'reject') {
            $product->update(['status' => Product::STATUS_REJECTED]);
        } else if ($this->actionType === 'hidden') {
            $product->update(['status' => Product::STATUS_HIDDEN]);
        }
        else if ($this->actionType === 'approve') {
            $product->update(['status' => Product::STATUS_APPROVED]);
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
