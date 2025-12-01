<?php

namespace App\Livewire\Seller\Products;

use App\Exports\ProductTemplateExport;
use App\Exports\ProductImportErrorExport; // <--- Nhớ dòng này
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

    public $category_id = '';
    public $excel_file;
    public $images = [];

    // *** SỬA: Biến này để chứa mảng các dòng lỗi (gồm data + lý do) ***
    public $errorRows = []; 

    public function mount()
    {
        $this->allCategories = Category::orderBy('name')->get();
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

    public function downloadTemplate()
    {
        $this->validate([
            'category_id' => 'required|exists:categories,id',
        ], ['category_id.required' => 'Vui lòng chọn danh mục trước.']);

        $category = Category::find($this->category_id);
        $filename = 'mau_nhap_' . $category->slug . '.xlsx';

        return Excel::download(new ProductTemplateExport($this->category_id), $filename);
    }

    // *** THÊM HÀM NÀY ĐỂ TẢI FILE LỖI ***
    public function downloadErrorFile()
    {
        if (empty($this->errorRows)) {
            return;
        }
        return Excel::download(new ProductImportErrorExport($this->errorRows), 'danh_sach_loi.xlsx');
    }

    public function import()
    {
        $this->validate([
            'category_id' => 'required|exists:categories,id',
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
            'images.*'   => 'nullable|image|max:5120',
        ]);

        // Reset lỗi cũ
        $this->errorRows = []; 

        $categoryId = $this->category_id;
        $sellerId = Auth::id();
        $filePath = $this->excel_file->path();

        // --- MAP VALIDATION ---
        $categoryAttributes = Attribute::whereHas('categories', function ($q) use ($categoryId) {
            $q->where('category_id', $categoryId);
        })->with('options')->get();

        $attributeMap = [];
        foreach ($categoryAttributes as $attr) {
            $attributeMap[$attr->name] = [
                'id' => $attr->id,
                'type' => $attr->type,
                'valid_values' => $attr->options->map(fn($opt) => mb_strtolower(trim($opt->value)))->toArray(),
                'original_values' => $attr->options->pluck('value', 'value')->toArray()
            ];
        }
        // -----------------------

        $uploadedImages = collect($this->images)->keyBy(fn($f) => $f->getClientOriginalName());

        DB::beginTransaction();
        $count = 0;
        $rowIndex = 1; 

        try {
            (new FastExcel())->import($filePath, function ($row) use (
                $categoryId, $sellerId, $attributeMap, $uploadedImages, &$count, &$rowIndex
            ) {
                $rowIndex++; 

                // 1. Check tên và giá
                if (empty($row['name']) || empty($row['price'])) {
                    // *** SỬA: LƯU CẢ DÒNG DATA VÀO ***
                    $this->errorRows[] = [
                        'data' => $row,
                        'error' => "Dòng $rowIndex: Thiếu tên hoặc giá sản phẩm"
                    ];
                    return null;
                }

                // 2. Check Brand
                $brandId = null;
                if (!empty($row['brand'])) {
                    $brandName = trim($row['brand']);
                    
                    $isValidBrand = Brand::where('name', $brandName)
                        ->whereHas('categories', function($q) use ($categoryId) {
                            $q->where('category_id', $categoryId);
                        })->exists();

                    if (!$isValidBrand) {
                        // *** SỬA: LƯU CẢ DÒNG DATA VÀO ***
                        $this->errorRows[] = [
                            'data' => $row,
                            'error' => "Dòng $rowIndex: Thương hiệu '$brandName' không hợp lệ/không thuộc danh mục này"
                        ];
                        return null; 
                    }
                    
                    $brand = Brand::where('name', $brandName)->first();
                    $brandId = $brand->id;
                }

                // 3. Check Attributes
                $attributesJson = [];
                foreach ($attributeMap as $attrName => $rules) {
                    if (!empty($row[$attrName])) {
                        $excelValue = trim($row[$attrName]);
                        
                        if ($rules['type'] === 'select') {
                            $checkValue = mb_strtolower($excelValue);
                            if (!in_array($checkValue, $rules['valid_values'])) {
                                // *** SỬA: LƯU CẢ DÒNG DATA VÀO ***
                                $this->errorRows[] = [
                                    'data' => $row,
                                    'error' => "Dòng $rowIndex: Thuộc tính '$attrName' có giá trị '$excelValue' không hợp lệ"
                                ];
                                return null; 
                            }
                            
                            foreach ($rules['original_values'] as $orgVal) {
                                if (mb_strtolower($orgVal) === $checkValue) {
                                    $attributesJson[$rules['id']] = $orgVal; 
                                    break;
                                }
                            }
                        } else {
                            $attributesJson[$rules['id']] = $excelValue;
                        }
                    }
                }

                // --- TẠO PRODUCT (Nếu chạy được xuống đây là OK) ---
                $staticData = [
                    'seller_id'   => $sellerId,
                    'category_id' => $categoryId,
                    'brand_id'    => $brandId,
                    'name'        => $row['name'],
                    'price'       => (float)$row['price'],
                    'description' => $row['description'] ?? null,
                ];

                $product = Product::create($staticData + ['attributes' => $attributesJson]);

                // --- XỬ LÝ ẢNH ---
                if ($uploadedImages->isNotEmpty()) {
                    foreach ($row as $key => $val) {
                        if (str_contains($key, 'image_') && !empty($val) && $uploadedImages->has($val)) {
                            $file = $uploadedImages->get($val);
                            $path = $file->store('product_images', 'public');
                            ProductImage::create(['product_id' => $product->id, 'image_path' => $path]);
                        }
                    }
                }

                $count++;
                return $product;
            });

            DB::commit();
            
            // Logic hiển thị thông báo
            if ($count > 0) {
                session()->flash('success', "Đã nhập thành công $count sản phẩm!");
            }
            
            // Kiểm tra mảng errorRows
            if (count($this->errorRows) > 0) {
                session()->flash('warning', 'Có ' . count($this->errorRows) . ' dòng bị lỗi. Hãy tải file báo lỗi để sửa.');
            } else {
                // Nếu không có lỗi nào thì reset form
                $this->reset(['excel_file', 'images']);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import Fail: ' . $e->getMessage());
            session()->flash('error', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('seller.products.Import')
               ->layout('layouts.SellerDashBoard');
    }
}