<?php

namespace App\Livewire\Admin\Products;

use App\Models\User;
use App\Models\Product;
use Livewire\Component;
use App\Models\Category;
use Illuminate\Support\Str;
use App\Models\Notification;
use Livewire\WithPagination;
use Rap2hpoutre\FastExcel\FastExcel;

class All extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // --- FILTERS ---
    public $search = '';
    public $status = '';
    public $category_id = '';
    public $seller_id = '';
    public $price_min = '';
    public $price_max = '';
    public $reason_filter = ''; // Lọc theo lý do
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // --- BULK ACTIONS ---
    public $selected = []; 
    public $selectAll = false;
    public $isBulk = false; 

    // --- MODAL ---
    public $selectedProductId;
    public $reasonModalOpen = false;
    public $actionType = 'reject'; 
    public $reasonType = ''; 
    public $reasonNote = ''; 

    protected $queryString = [
        'search', 'status', 'category_id', 'seller_id', 
        'reason_filter', 'price_min', 'price_max', 
        'sortField', 'sortDirection'
    ];

    public function updating($field) { $this->resetPage(); }

    // --- LOGIC CHECKBOX HÀNG LOẠT ---
    public function updatedSelectAll($value)
    {
        if ($value) {
            // Lấy toàn bộ ID của trang hiện tại hoặc query (tùy nhu cầu)
            // Ở đây tao lấy ID string để Livewire dễ xử lý
            $this->selected = $this->getProductsQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function sortBy($field)
    {
        $this->sortDirection = $this->sortField === $field 
            ? ($this->sortDirection === 'asc' ? 'desc' : 'asc') 
            : 'asc';
        $this->sortField = $field;
    }

    public function export()
    {
        $products = $this->getProductsQuery()->get();
        return (new FastExcel($products))->download('products.xlsx');
    }

    // --- QUERY CHÍNH (Đã thêm lọc lý do) ---
    protected function getProductsQuery()
    {
        return Product::query()
            ->with(['seller', 'category'])
            ->when($this->search, function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%")
                  ->orWhereHas('seller', fn($q) => $q->where('name', 'like', "%{$this->search}%"));
            })
            // LỌC THEO LÝ DO TỪ CHỐI
            ->when($this->reason_filter, function($q) {
                $q->whereHas('notifications', fn($sub) => 
                    $sub->where('message', 'like', "%{$this->reason_filter}%")
                );
            })
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->category_id, fn($q) => $q->where('category_id', $this->category_id))
            ->when($this->seller_id, fn($q) => $q->where('seller_id', $this->seller_id))
            ->when($this->price_min !== '', fn($q) => $q->where('price', '>=', $this->price_min))
            ->when($this->price_max !== '', fn($q) => $q->where('price', '<=', $this->price_max))
            ->orderBy($this->sortField, $this->sortDirection);
    }

    // --- MỞ MODAL (Xử lý cả Lẻ và Hàng loạt) ---
    // Nếu $id = null -> Hiểu là đang bấm nút Hàng loạt
    public function openRejectModal($id = null, $type = 'reject')
    {
        $this->actionType = $type;
        $this->reasonType = ''; 
        $this->reasonNote = '';
        $this->reasonModalOpen = true;

        if ($id) {
            // Chế độ Lẻ
            $this->selectedProductId = $id;
            $this->isBulk = false;
        } else {
            // Chế độ Hàng loạt
            if (empty($this->selected)) {
                $this->reasonModalOpen = false;
                $this->dispatch('alert', ['type' => 'error', 'message' => 'Chưa chọn sản phẩm nào!']);
                return;
            }
            $this->isBulk = true;
        }
    }

    public function closeRejectModal()
    {
        $this->reasonModalOpen = false;
        $this->reasonType = '';
        $this->reasonNote = '';
        $this->selectedProductId = null;
        $this->isBulk = false;
    }

    // --- XÁC NHẬN (CORE LOGIC) ---
    public function confirmReject()
    {
        // 1. Validate
        if ($this->actionType !== 'approve') {
             $this->validate(['reasonType' => 'required|string']);
        }

        // 2. Chuẩn bị lý do
        $fullReason = $this->reasonType;
        if (!empty($this->reasonNote)) {
            $fullReason .= " - " . $this->reasonNote;
        }
        
        // Nếu duyệt mà không ghi lý do thì set default
        if ($this->actionType === 'approve' && empty($fullReason)) {
            $fullReason = 'Sản phẩm hợp lệ / Đã khắc phục vi phạm.';
        }

        // 3. Xác định danh sách ID cần xử lý
        // Nếu là Bulk thì lấy mảng $selected, nếu Lẻ thì lấy [$selectedProductId]
        $ids = $this->isBulk ? $this->selected : [$this->selectedProductId];
        
        $products = Product::whereIn('id', $ids)->get();
        $statusMsg = '';

        foreach ($products as $product) {
            // Update status
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

            // Tạo thông báo
            Notification::create([
                'user_id' => $product->seller_id,
                'type' => 'product_' . $this->actionType,
                'message' => "Sản phẩm '{$product->name}' {$statusMsg}. Lý do: {$fullReason}",
                'is_read' => false,
            ]);
        }

        // 4. Reset & Close
        $this->selected = [];
        $this->selectAll = false;
        $this->closeRejectModal();
        
        session()->flash('success', "Đã xử lý xong " . count($ids) . " sản phẩm!");
    }

    public function render()
    {
        $products = $this->getProductsQuery()->paginate(20)->withQueryString();

        // Stats & Charts (Giữ nguyên logic cũ của mày)
        $stats = [
            'total' => Product::count(),
            'approved' => Product::where('status', 'approved')->count(),
            'pending' => Product::where('status', 'pending')->count(),
            'rejected' => Product::where('status', 'rejected')->count(),
        ];
        $stats['approved_percent'] = $stats['total'] > 0 ? round(($stats['approved'] / $stats['total']) * 100, 1) : 0;

        $topCategories = Category::withCount('products')->orderBy('products_count', 'desc')->limit(6)->get();
        $chartData = $topCategories->map(fn($c) => ['label'=>$c->name, 'value'=>$c->products_count, 'color'=>'#'.substr(md5($c->id),0,6)])->toJson();

        $reasonOptions = [
            'Hình ảnh mờ/kém chất lượng',
            'Sản phẩm vi phạm bản quyền',
            'Nội dung không phù hợp/phản cảm',
            'Giá sai quy định thị trường',
            'Thông tin mô tả sai lệch',
            'Spam/Đăng trùng lặp',
            'Khác'
        ];

        return view('admin.products.all', [
            'products' => $products,
            'reasonOptions' => $reasonOptions,
            'categories' => Category::orderBy('name')->get(),
            'users' => User::where('role', 'seller')->orderBy('name')->get(),
            'stats' => $stats,
            'chartData' => $chartData,
        ]);
    }
}