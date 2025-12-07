<?php

namespace App\Livewire\Seller\Stock;

use Livewire\Component;
use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

class SellerTransactionHistory extends Component
{
    use WithPagination;

    // --- FIX 1: Khai báo Theme để không bị vỡ giao diện phân trang ---
    protected $paginationTheme = 'bootstrap';

    // --- FIX 2: Giữ bộ lọc trên URL khi F5 hoặc chuyển trang ---
    protected $queryString = [
        'filterType' => ['except' => ''],
        'filterProduct' => ['except' => ''],
    ];

    // --- Thuộc tính Filter & History ---
    public $filterType = '';
    public $filterProduct = '';

    // --- Thuộc tính Modal Nhập kho ---
    public $showImportModal = false;
    public $importableProducts = [];
    public $importItems = [];
    public $import_notes = '';

    // --- Thuộc tính Modal Xuất kho ---
    public $showExportModal = false;
    public $export_product_id = '';
    public $export_quantity = 0;
    public $export_notes = '';
    
    public function mount()
    {
        $this->importableProducts = Product::where('seller_id', Auth::id())
                                           ->orderBy('name')
                                           ->get(['id', 'name']);

        // Check URL xem có cần mở modal ngay không
        $action = request()->query('action', '');
        $prefillProductId = request()->query('product_id', '');

        if ($action === 'import' && $prefillProductId) {
            $this->openImportModal($prefillProductId);
        } elseif ($action === 'export' && $prefillProductId) {
            $this->openExportModal($prefillProductId);
        }
    }

    public function updatingFilterType() { $this->resetPage(); }
    public function updatingFilterProduct() { $this->resetPage(); }

    // --- MODAL NHẬP KHO ---
    public function openImportModal($productId = null)
    {
        $this->resetErrorBag();
        $this->import_notes = '';
        // Mặc định có 1 dòng để nhập luôn
        $this->importItems = [
            ['product_id' => $productId ?? '', 'quantity' => 1]
        ];
        $this->showImportModal = true;
    }

    // FIX 3: Đóng là phải Reset sạch sẽ
    public function closeImportModal() 
    { 
        $this->showImportModal = false; 
        $this->importItems = [];
        $this->import_notes = '';
        $this->resetErrorBag();
    }

    public function addImportItem()
    {
        $this->importItems[] = ['product_id' => '', 'quantity' => 1];
    }
    
    public function removeImportItem($index)
    {
        unset($this->importItems[$index]);
        $this->importItems = array_values($this->importItems);
    }

    // --- MODAL XUẤT KHO ---
    public function openExportModal($productId = null)
    {
        $this->resetErrorBag();
        $this->export_product_id = $productId ?? ''; 
        $this->export_quantity = 1; // Để mặc định là 1 cho tiện
        $this->export_notes = '';
        $this->showExportModal = true;
    }

    // FIX 3: Đóng là phải Reset sạch sẽ
    public function closeExportModal() 
    { 
        $this->showExportModal = false; 
        $this->export_product_id = '';
        $this->export_quantity = 0;
        $this->export_notes = '';
        $this->resetErrorBag();
    }

    // --- LƯU NHẬP KHO ---
    public function saveImport()
    {
        $validated = $this->validate([
            'importItems' => 'required|array|min:1',
            'importItems.*.product_id' => 'required|exists:products,id',
            'importItems.*.quantity' => 'required|integer|min:1',
            'import_notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['importItems'] as $item) {
                $product = Product::where('id', $item['product_id'])
                                  ->where('seller_id', Auth::id())
                                  ->first();
                
                if (!$product) continue; // Bỏ qua nếu không tìm thấy (an toàn hơn throw lỗi)

                // 1. Tạo log
                InventoryTransaction::create([
                    'product_id' => $product->id,
                    'seller_id' => Auth::id(),
                    'transaction_type' => 'import',
                    'quantity' => $item['quantity'],
                    'notes' => $validated['import_notes'],
                ]);

                // 2. Tăng tồn kho
                $product->increment('stock', $item['quantity']);
            }

            DB::commit();
            session()->flash('success', 'Nhập kho thành công!');
            $this->closeImportModal(); // Đóng và reset form

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    // --- LƯU XUẤT KHO ---
    public function saveExport()
    {
        $validated = $this->validate([
            'export_product_id' => 'required|exists:products,id',
            'export_quantity' => 'required|integer|min:1',
            'export_notes' => 'required|string|max:500', 
        ]);

        $product = Product::where('id', $validated['export_product_id'])
                          ->where('seller_id', Auth::id())
                          ->firstOrFail();

        if ($product->stock < $validated['export_quantity']) {
            $this->addError('export_quantity', 'Tồn kho không đủ (còn: ' . $product->stock . ').');
            return;
        }

        DB::beginTransaction();
        try {
            // 1. Tạo log
            InventoryTransaction::create([
                'product_id' => $product->id,
                'seller_id' => Auth::id(),
                'transaction_type' => 'export',
                'quantity' => $validated['export_quantity'],
                'notes' => $validated['export_notes'],
            ]);

            // 2. Trừ tồn kho
            $product->decrement('stock', $validated['export_quantity']);

            DB::commit();
            session()->flash('success', 'Xuất kho thành công!');
            $this->closeExportModal(); // Đóng và reset form

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $transactionsQuery = InventoryTransaction::query()
            ->where('seller_id', Auth::id()) 
            ->with('product'); 

        if ($this->filterType) {
            $transactionsQuery->where('transaction_type', $this->filterType);
        }
        if ($this->filterProduct) {
            $transactionsQuery->whereHas('product', function ($query) {
                $query->where('name', 'like', '%' . $this->filterProduct . '%');
            });
        }

        $transactions = $transactionsQuery->orderBy('created_at', 'desc')->paginate(15);

        return view('livewire.seller.stock.seller-transaction-history', [
            'transactions' => $transactions,
        ])->layout('layouts.SellerDashBoard');
    }
}