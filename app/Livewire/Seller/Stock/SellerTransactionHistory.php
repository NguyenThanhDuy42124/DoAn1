<?php

namespace App\Livewire\Seller\Stock;

use Livewire\Component;
use App\Models\InventoryTransaction;
use App\Models\Product; // <-- Thêm model Product
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // <-- Thêm DB
use Livewire\WithPagination;

class SellerTransactionHistory extends Component
{
    use WithPagination;

    // --- Thuộc tính Filter & History (Giữ nguyên) ---
    public $filterType = '';
    public $filterProduct = '';

    // --- Thuộc tính cho Modal Nhập kho ---
    public $showImportModal = false;
    public $importableProducts = []; // Danh sách sản phẩm để chọn
    public $importItems = [];
    public $import_notes = '';

    // --- Thuộc tính cho Modal Xuất kho ---
    public $showExportModal = false;
    public $export_product_id = '';
    public $export_quantity = 0;
    public $export_notes = '';
    
    /**
     * Khởi chạy component, nhận tham số từ URL
     */
    public function mount()
    {
        // Lấy danh sách sản phẩm của seller để nạp vào dropdown
        $this->importableProducts = Product::where('seller_id', Auth::id())
                                           ->orderBy('name')
                                           ->get(['id', 'name']); // Chỉ lấy id và name cho nhẹ

        // Lấy tham số từ URL
        $action = request()->query('action', '');
        $prefillProductId = request()->query('product_id', '');

        if ($action === 'import' && $prefillProductId) {
            $this->openImportModal($prefillProductId);
        } elseif ($action === 'export' && $prefillProductId) {
            $this->openExportModal($prefillProductId);
        }
    }

    // --- Reset trang khi filter (Giữ nguyên) ---
    public function updatingFilterType() { $this->resetPage(); }
    public function updatingFilterProduct() { $this->resetPage(); }

    // --- Logic Mở/Đóng Modal ---
    public function openImportModal($productId = null)
    {
        $this->resetErrorBag();
        $this->import_notes = '';
        $this->importItems = [
            ['product_id' => $productId ?? '', 'quantity' => 1]
        ];
        $this->showImportModal = true;
    }
    public function closeImportModal() { $this->showImportModal = false; }

    public function addImportItem()
    {
        // Thêm một dòng rỗng vào mảng
        $this->importItems[] = ['product_id' => '', 'quantity' => 1];
    }
    public function removeImportItem($index)
    {
        unset($this->importItems[$index]);
        $this->importItems = array_values($this->importItems); // Sắp xếp lại index
    }

    public function openExportModal($productId = null)
    {
        $this->resetErrorBag();
        $this->export_product_id = $productId ?? ''; // Tự chọn sản phẩm nếu được truyền
        $this->export_quantity = 0;
        $this->export_notes = '';
        $this->showExportModal = true;
    }
    public function closeExportModal() { $this->showExportModal = false; }


    // --- Logic LƯU NHẬP KHO ---
    public function saveImport()
    {
        $validated = $this->validate([
            // Sửa: Validate mảng $importItems
            'importItems' => 'required|array|min:1',
            'importItems.*.product_id' => 'required|exists:products,id',
            'importItems.*.quantity' => 'required|integer|min:1',
            'import_notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // Sửa: Lặp qua mảng $importItems
            foreach ($validated['importItems'] as $item) {
                // Đảm bảo sản phẩm này thuộc seller
                $product = Product::where('id', $item['product_id'])
                                  ->where('seller_id', Auth::id())
                                  ->first();
                
                if (!$product) {
                    // Nếu sản phẩm không thuộc seller, báo lỗi và rollback
                    throw new \Exception('Sản phẩm ID ' . $item['product_id'] . ' không hợp lệ.');
                }

                // 1. Tạo log
                InventoryTransaction::create([
                    'product_id' => $product->id,
                    'seller_id' => Auth::id(),
                    'transaction_type' => 'import',
                    'quantity' => $item['quantity'],
                    'notes' => $validated['import_notes'], // Dùng ghi chú chung cho tất cả
                ]);

                // 2. Cập nhật tồn kho
                $product->increment('stock', $item['quantity']);
            }

            DB::commit();
            session()->flash('success', 'Nhập kho hàng loạt thành công!');
            $this->closeImportModal();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    // --- Logic LƯU XUẤT KHO ---
    public function saveExport()
    {
        $validated = $this->validate([
            'export_product_id' => 'required|exists:products,id',
            'export_quantity' => 'required|integer|min:1',
            'export_notes' => 'required|string|max:500', // Bắt buộc lý do khi xuất
        ]);

        // Đảm bảo sản phẩm này thuộc seller
        $product = Product::where('id', $validated['export_product_id'])
                          ->where('seller_id', Auth::id())
                          ->firstOrFail();

        // Kiểm tra tồn kho trước khi cho xuất
        if ($product->stock < $validated['export_quantity']) {
            $this->addError('export_quantity', 'Số lượng xuất không thể lớn hơn tồn kho (hiện có: ' . $product->stock . ').');
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

            // 2. Cập nhật tồn kho (dùng decrement để an toàn)
            $product->decrement('stock', $validated['export_quantity']);

            DB::commit();
            session()->flash('success', 'Xuất kho thành công!');
            $this->closeExportModal();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Lỗi: ' . $e->getMessage());
        }
    }


    /**
     * Render (Giữ nguyên logic query, chỉ khác view)
     */
    public function render()
    {
        // Lấy query builder (giữ nguyên)
        $transactionsQuery = InventoryTransaction::query()
            ->where('seller_id', Auth::id()) 
            ->with('product'); 

        // Áp dụng filter (giữ nguyên)
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
