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

    // thêm theme để Livewire render pagination tương thích bootstrap
    protected $paginationTheme = 'bootstrap';

    // Search & Filters
    public $search = '';
    public $status = '';
    public $category_id = '';
    public $seller_id = '';
    public $price_min = '';
    public $price_max = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';


// --- PHẦN XỬ LÝ MODAL & LÝ DO ---
    public $selectedProductId;
    public $reasonModalOpen = false;
    public $actionType = 'reject'; 
    
    // Tách biến ra để Query được như ý mày muốn
    public $reasonType = ''; // Lưu lý do chọn từ Select
    public $reasonNote = ''; // Lưu ghi chú viết tay


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
        // preserve query string khi phân trang
        $products = $this->getProductsQuery()->paginate(20)->withQueryString();

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

        $reasonOptions = [
            'Hình ảnh mờ/kém chất lượng',
            'Sản phẩm vi phạm bản quyền',
            'Nội dung không phù hợp/phản cảm',
            'Giá sai quy định thị trường',
            'Thông tin mô tả sai lệch',
            'Spam/Đăng trùng lặp',
        ];

        $chartData = $topCategories->map(function ($cat) {
            return [
                'label' => $cat->name,
                'value' => $cat->products_count,
                'color' => '#' . substr(md5($cat->id), 0, 6),
            ];
        })->toJson();

        return view('admin.products.all', [
            'products' => $products,
            'reasonOptions' => $reasonOptions,
            'categories' => Category::orderBy('name')->get(),
            'users' => User::where('role', 'seller')->orderBy('name')->get(),
            'stats' => $stats,
            'chartData' => $chartData,
        ]);
    }
    public function openRejectModal($id, $type = 'reject')
    {
        $this->selectedProductId = $id;
        $this->actionType = $type; // Lưu lại hành động: reject/hidden/approve
        
        // Reset form
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
    }
    public function confirmReject()
    {
        // Nếu là Approve (Duyệt/Khôi phục) thì không bắt buộc lý do
        if ($this->actionType === 'approve') {
            $this->validate(['actionType' => 'required']);
        } else {
            // Nếu Reject hoặc Hide thì bắt buộc chọn lý do chính
            $this->validate([
                'reasonType' => 'required|string', 
                'actionType' => 'required',
            ]);
        }

        $product = Product::findOrFail($this->selectedProductId);
        $statusMsg = '';

        // Xử lý status
        if ($this->actionType === 'reject') {
            $product->update(['status' => Product::STATUS_REJECTED]);
            $statusMsg = 'bị từ chối';
        } else if ($this->actionType === 'hidden') {
            $product->update(['status' => Product::STATUS_HIDDEN]);
            $statusMsg = 'bị ẩn';
        } else if ($this->actionType === 'approve') {
            $product->update(['status' => Product::STATUS_APPROVED]);
            $statusMsg = 'được duyệt/khôi phục';
        }

        // GỘP LÝ DO ĐỂ LƯU VÀO DB (Notification)
        // Format: "Lý do chính - Ghi chú thêm" -> Dễ query LIKE sau này
        $fullReason = $this->reasonType;
        if (!empty($this->reasonNote)) {
            $fullReason .= " - " . $this->reasonNote;
        }
        
        // Nếu là approve mà không ghi gì thì set default
        if ($this->actionType === 'approve' && empty($fullReason)) {
            $fullReason = 'Sản phẩm hợp lệ.';
        }

        Notification::create([
            'user_id' => $product->seller_id,
            'type' => 'product_' . $this->actionType,
            'message' => "Sản phẩm '{$product->name}' {$statusMsg}. Lý do: {$fullReason}",
            'is_read' => false,
        ]);

        $this->closeRejectModal();
        session()->flash('success', 'Đã xử lý xong!');
    }

}
