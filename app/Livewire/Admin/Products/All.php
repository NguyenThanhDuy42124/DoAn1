<?php

namespace App\Livewire\Admin\Products;

use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Rap2hpoutre\FastExcel\FastExcel;

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
}