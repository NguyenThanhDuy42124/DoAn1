<?php

namespace App\Livewire\Seller\Products;

use App\Exports\ProductTemplateExport;
use App\Exports\ProductBulkEditExport;
use App\Exports\ProductImportErrorExport;
use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Rap2hpoutre\FastExcel\FastExcel;

class ImportProducts extends Component
{
    use WithFileUploads;

    public Collection $allCategories;
    public Collection $previewAttributes;

    public $activeTab = 'new'; 

    public $category_id = '';
    public $excel_file;
    public $images = [];
    public $errorRows = [];

    public function mount()
    {
        $this->allCategories = Category::orderBy('name')->get();
        $this->previewAttributes = collect();
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->reset(['category_id', 'excel_file', 'images', 'errorRows']);
        $this->previewAttributes = collect();
    }

    public function updatedCategoryId($value)
    {
        if ($value) {
            $this->previewAttributes = Attribute::whereHas('categories', function ($q) use ($value) {
                $q->where('category_id', $value);
            })->orderBy('name')->get();
        } else {
            $this->previewAttributes = collect();
        }
    }

    // --- 1. TẢI FILE MẪU ---
    public function downloadTemplate()
    {
        $this->validate(['category_id' => 'required|exists:categories,id']);
        $category = Category::find($this->category_id);
        return Excel::download(new ProductTemplateExport($this->category_id), 'mau_nhap_' . $category->slug . '.xlsx');
    }

    // --- 2. XUẤT DỮ LIỆU CŨ ---
    public function downloadExportData()
    {
        $this->validate(['category_id' => 'required|exists:categories,id']);
        $category = Category::find($this->category_id);
        return Excel::download(new ProductBulkEditExport($this->category_id, Auth::id()), 'data_update_' . $category->slug . '.xlsx');
    }

    // --- 3. TẢI FILE BÁO LỖI ---
    public function downloadErrorFile()
    {
        if (empty($this->errorRows)) return;
        return Excel::download(new ProductImportErrorExport($this->errorRows), 'danh_sach_loi.xlsx');
    }

    // --- SUBMIT ---
    public function submit()
    {
        if ($this->activeTab === 'new') {
            $this->import();
        } else {
            $this->importUpdate();
        }
    }

    // --- HÀM NHẬP MỚI ---
    public function import()
    {
        $this->validate([
            'category_id' => 'required|exists:categories,id',
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
            'images.*'   => 'nullable|image|max:5120',
        ]);

        $this->errorRows = []; 
        $categoryId = $this->category_id;
        $sellerId = Auth::id();
        $filePath = $this->excel_file->path();

        $attributeMap = $this->getAttributeMap($categoryId);
        $uploadedImages = collect($this->images)->keyBy(fn($f) => $f->getClientOriginalName());

        DB::beginTransaction();
        $count = 0;
        $rowIndex = 1; 

        try {
            (new FastExcel())->import($filePath, function ($row) use (
                $categoryId, $sellerId, $attributeMap, $uploadedImages, &$count, &$rowIndex
            ) {
                $rowIndex++; 

                if (empty($row['name']) || empty($row['price'])) {
                    $this->errorRows[] = ['data' => $row, 'error' => "Dòng $rowIndex: Thiếu tên hoặc giá sản phẩm"];
                    return null;
                }

                // SỬA: Truyền thêm $row vào để khi lỗi thì lưu lại đúng dòng đó
                $brandId = $this->validateBrand($row['brand'] ?? '', $categoryId, $rowIndex, $row);
                if ($brandId === false) return null;

                $attributesJson = $this->validateAttributes($row, $attributeMap, $rowIndex);
                if ($attributesJson === false) return null;

                $product = Product::create([
                    'seller_id'   => $sellerId,
                    'category_id' => $categoryId,
                    'brand_id'    => $brandId,
                    'name'        => $row['name'],
                    'price'       => (float)$row['price'],
                    'description' => $row['description'] ?? null,
                    'attributes'  => $attributesJson,
                ]);

                $this->processImages($product, $row, $uploadedImages);

                $count++;
                return $product;
            });

            DB::commit();
            $this->handlePostImport($count);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import New Fail: ' . $e->getMessage());
            session()->flash('error', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }

    // --- HÀM CẬP NHẬT ---
    public function importUpdate()
    {
        $this->validate([
            'category_id' => 'required|exists:categories,id',
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
            'images.*'   => 'nullable|image|max:5120',
        ]);

        $this->errorRows = []; 
        $categoryId = $this->category_id;
        $sellerId = Auth::id();
        $filePath = $this->excel_file->path();

        $attributeMap = $this->getAttributeMap($categoryId);
        $uploadedImages = collect($this->images)->keyBy(fn($f) => $f->getClientOriginalName());

        DB::beginTransaction();
        $count = 0; 
        $rowIndex = 1; 

        try {
            (new FastExcel())->import($filePath, function ($row) use (
                $sellerId, $categoryId, $attributeMap, $uploadedImages, &$count, &$rowIndex 
            ) {
                $rowIndex++;

                if (empty($row['id'])) {
                    $this->errorRows[] = ['data' => $row, 'error' => "Dòng $rowIndex: Thiếu ID (Bắt buộc khi update)."];
                    return null;
                }

                $product = Product::where('id', $row['id'])->where('seller_id', $sellerId)->first();
                if (!$product) {
                    $this->errorRows[] = ['data' => $row, 'error' => "Dòng $rowIndex: Không tìm thấy SP ID {$row['id']} hoặc không có quyền."];
                    return null;
                }

                // SỬA: Truyền thêm $row vào
                $brandId = $this->validateBrand($row['brand'] ?? '', $categoryId, $rowIndex, $row);
                if ($brandId === false) return null;

                $attributesJson = $this->validateAttributes($row, $attributeMap, $rowIndex);
                if ($attributesJson === false) return null;

                $updateData = [
                    'name'        => $row['name'],
                    'price'       => (float)$row['price'],
                    'description' => $row['description'] ?? null,
                    'attributes'  => $attributesJson,
                ];
                if ($brandId) $updateData['brand_id'] = $brandId;

                $product->update($updateData);

                $this->processImages($product, $row, $uploadedImages);

                $count++;
                return $product;
            });

            DB::commit();
            $this->handlePostImport($count);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update Fail: ' . $e->getMessage());
            session()->flash('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    // --- HELPERS ---

    private function getAttributeMap($categoryId)
    {
        $categoryAttributes = Attribute::whereHas('categories', function ($q) use ($categoryId) {
            $q->where('category_id', $categoryId);
        })->with('options')->get();

        $map = [];
        foreach ($categoryAttributes as $attr) {
            $map[$attr->name] = [
                'id' => $attr->id,
                'type' => $attr->type,
                'valid_values' => $attr->options->map(fn($opt) => mb_strtolower(trim($opt->value)))->toArray(),
                'original_values' => $attr->options->pluck('value', 'value')->toArray()
            ];
        }
        return $map;
    }

    // SỬA: Thêm tham số $row vào hàm này
    private function validateBrand($brandName, $categoryId, $rowIndex, $row)
    {
        if (empty($brandName)) return null;
        
        $brandName = trim($brandName);
        $isValid = Brand::where('name', $brandName)
            ->whereHas('categories', fn($q) => $q->where('category_id', $categoryId))
            ->exists();

        if (!$isValid) {
            // SỬA: Dùng $row thay vì request()->all()
            $this->errorRows[] = ['data' => $row, 'error' => "Dòng $rowIndex: Thương hiệu '$brandName' sai."]; 
            return false;
        }
        return Brand::where('name', $brandName)->first()->id;
    }

    private function validateAttributes($row, $map, $rowIndex)
    {
        $json = [];
        foreach ($map as $attrName => $rules) {
            if (!empty($row[$attrName])) {
                $val = trim($row[$attrName]);
                if ($rules['type'] === 'select') {
                    $check = mb_strtolower($val);
                    if (!in_array($check, $rules['valid_values'])) {
                        $this->errorRows[] = ['data' => $row, 'error' => "Dòng $rowIndex: '$attrName' giá trị '$val' sai."];
                        return false;
                    }
                    foreach ($rules['original_values'] as $org) {
                        if (mb_strtolower($org) === $check) {
                            $json[$rules['id']] = $org;
                            break;
                        }
                    }
                } else {
                    $json[$rules['id']] = $val;
                }
            }
        }
        return $json;
    }

    private function processImages($product, $row, $uploadedImages)
    {
        for ($i = 1; $i <= 20; $i++) {
            $col = "image_" . $i;
            $name = trim($row[$col] ?? '');
            
            if (!empty($name) && $uploadedImages->has($name)) {
                $file = $uploadedImages->get($name);
                $path = $file->store('product_images', 'public');
                ProductImage::create(['product_id' => $product->id, 'image_path' => $path]);
            }
        }
    }

    private function handlePostImport($count)
    {
        if ($count > 0) session()->flash('success', "Thành công $count sản phẩm!");
        
        if (count($this->errorRows) > 0) {
            session()->flash('warning', 'Có ' . count($this->errorRows) . ' dòng lỗi. Tải file lỗi để xem.');
        } else {
            $this->reset(['excel_file', 'images']);
        }
    }

    public function render()
    {
        return view('seller.products.Import')->layout('layouts.SellerDashBoard');
    }
}