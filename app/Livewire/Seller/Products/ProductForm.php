<?php

namespace App\Livewire\Seller\Products;

use Livewire\Component;
use Livewire\WithFileUploads; // <-- Thêm để xử lý upload ảnh
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Attribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Rule; // <-- Dùng Rule Attributes cho validation
use Illuminate\Support\Collection; // <-- Thêm

class ProductForm extends Component
{
    use WithFileUploads;

    // --- Thuộc tính chính ---
    public ?Product $product; // Model Product (có thể rỗng nếu là create)
    public $productId = null; // ID sản phẩm (nếu là edit)

    // --- Dữ liệu Form (bind với wire:model) ---
    #[Rule('required|exists:categories,id')]
    public $category_id = ''; // Dùng riêng để trigger updated hook

    #[Rule('required|string|max:255')]
    public $name = '';

    #[Rule('required|numeric|min:0')]
    public $price = '';

    #[Rule('nullable|exists:brands,id')]
    public $brand_id = '';

    #[Rule('required|integer|min:0')]
    public $stock = 0;

    #[Rule('nullable|string')]
    public $description = '';

    // Mảng lưu giá trị thuộc tính động, ví dụ: ['attributes'][1] = 'Core i7'
    public $attributeValues = [];

    // Ảnh sản phẩm
    // Sử dụng computed property cho rule ảnh để tùy chỉnh
    public $images = []; // Mảng chứa các file ảnh MỚI upload (TemporaryUploadedFile)
    public $existingImages = []; // Mảng chứa ảnh CŨ (cho edit)
    public $deletedImageIds = []; // Mảng ID ảnh CŨ cần xóa

    // --- Dữ liệu phụ trợ (load 1 lần) ---
    public Collection $allCategories; // <-- Dùng Collection type hint
    public Collection $allBrands;     // <-- Dùng Collection type hint
    public Collection $categoryAttributes; // Thuộc tính động sẽ load ở đây

    /**
     * Khởi chạy component (Xử lý cả Create và Edit)
     */
    // GIỮ LẠI TYPE HINT Product, THÊM DẤU ?
   public function mount($productId = null)
    {
        // Debug
        // dd('Mount received productId:', $productId);
        // Load Categories và Brands
        $this->allCategories = Category::whereDoesntHave('children')->orderBy('name')->get();
        $this->allBrands = Brand::orderBy('name')->get();
        $this->categoryAttributes = collect();

        // TỰ TÌM Product nếu có productId
        if ($productId) {
            // Dùng findOrFail để tự 404 nếu ID sai
            $productModel = Product::with(['images', 'attributeValues'])->findOrFail($productId);

            // Gán vào thuộc tính component
            $this->product = $productModel;
            $this->productId = $productModel->id; // Gán lại cho chắc

            // Fill dữ liệu (Logic cũ giữ nguyên)
            $this->category_id = $productModel->category_id;
            $this->name = $productModel->name;
            $this->price = $productModel->price;
            $this->brand_id = $productModel->brand_id;
            $this->stock = $productModel->stock;
            $this->description = $productModel->description;
            $this->existingImages = $productModel->images->toArray();
            $this->loadCategoryAttributes();
            $this->attributeValues = $productModel->attributeValues->pluck('value', 'attribute_id')->toArray();

        } else { // Nếu là Create (productId là null)
            $this->product = new Product(); // Tạo model rỗng
        }
    }

    /**
     * Chạy khi $category_id thay đổi (wire:model.live)
     */
    public function updatedCategoryId($value)
    {
       
        $this->loadCategoryAttributes();
        // Reset giá trị attributes cũ khi đổi danh mục
        $this->attributeValues = [];
    }

    /**
     * Helper: Tải thuộc tính dựa trên $category_id hiện tại
     */
    public function loadCategoryAttributes()
    {
        if (!empty($this->category_id)) {
            $this->categoryAttributes = Attribute::whereHas('categories', function($q) {
                $q->where('category_id', $this->category_id);
            })->with('options') // Load sẵn options cho select
              ->orderBy('name') // Hoặc sort_order_in_group nếu có
              ->get();
        } else {
            $this->categoryAttributes = collect(); // Trả về collection rỗng
        }
    }

     /**
      * Rule validation động cho ảnh
      */
     protected function rules()
     {
         return [
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'brand_id'    => 'nullable|exists:brands,id',
            'stock'       => 'required|integer|min:0',
            'description' => 'nullable|string',
            'attributeValues'  => 'nullable|array',
            'attributeValues.*'=> 'nullable|string|max:255',
            // Rule cho ảnh: Chỉ validate ảnh MỚI upload
            'images.*'    => 'nullable|image|max:2048', // 2MB
         ];
     }

    /**
     * Xóa ảnh CŨ (đã lưu trong DB)
     */
    public function removeExistingImage($imageId)
    {
        $this->deletedImageIds[] = $imageId; // Thêm ID vào danh sách cần xóa
        // Lọc bỏ ảnh khỏi mảng hiển thị
        $this->existingImages = array_filter($this->existingImages, fn($img) => $img['id'] != $imageId);
    }

    /**
     * Xóa ảnh MỚI (chưa lưu, đang preview)
     */
     public function removeNewImage($index)
     {
         array_splice($this->images, $index, 1);
     }


    /**
     * Lưu sản phẩm (Create hoặc Update)
     */
    public function save()
    {
    
        // Validate dữ liệu cơ bản + ảnh mới upload
        $validatedData = $this->validate();

        DB::beginTransaction();
        try {
            // --- 1. Lưu/Cập nhật thông tin cơ bản ---
            $productData = [
                'category_id' => $validatedData['category_id'],
                'brand_id'    => $validatedData['brand_id'] ?: null, // Dùng null nếu rỗng
                'name'        => $validatedData['name'],
                'price'       => $validatedData['price'],
                'stock'       => $validatedData['stock'],
                'description' => $validatedData['description'],
                'seller_id'   => Auth::id(), // Luôn lấy ID seller đang login
                // Status: Mặc định là 'Pending' khi tạo mới
                'status'      => $this->product->exists ? $this->product->status : Product::STATUS_PENDING,
            ];

            if ($this->productId) { // Update
                $this->product->update($productData);
            } else { // Create
                $this->product = Product::create($productData);
            }

            // --- 2. Lưu/Cập nhật thuộc tính EAV ---
            $this->product->attributeValues()->delete(); // Xóa hết thuộc tính cũ
            if (!empty($validatedData['attributeValues'])) {
                $attributeValuesToInsert = [];
                foreach ($validatedData['attributeValues'] as $attributeId => $value) {
                    // Chỉ thêm nếu có giá trị và thuộc tính đó thuộc danh mục hiện tại
                    if (!empty($value) && $this->categoryAttributes->contains('id', $attributeId)) {
                        $attributeValuesToInsert[] = [
                            'product_id' => $this->product->id,
                            'attribute_id' => $attributeId,
                            'value' => $value,
                            // created_at, updated_at tự động
                        ];
                    }
                }
                if (!empty($attributeValuesToInsert)) {
                    ProductAttributeValue::insert($attributeValuesToInsert); // Insert hàng loạt
                }
            }

            // --- 3. Xử lý ảnh CŨ cần xóa ---
            if (!empty($this->deletedImageIds)) {
                $imagesToDelete = ProductImage::whereIn('id', $this->deletedImageIds)
                                               ->where('product_id', $this->product->id)
                                               ->get();
                foreach ($imagesToDelete as $image) {
                    Storage::disk('public')->delete($image->image_path); // Xóa file
                    $image->delete(); // Xóa record DB
                }
                $this->deletedImageIds = []; // Reset mảng
            }

            // --- 4. Xử lý ảnh MỚI upload ---
            if (!empty($this->images)) {
                foreach ($this->images as $imageFile) {
                    // Lưu file ảnh vào storage/app/public/product_images
                    $path = $imageFile->store('product_images', 'public');
                    // Tạo record trong DB
                    ProductImage::create([
                        'product_id' => $this->product->id,
                        'image_path' => $path,
                    ]);
                }
                 $this->images = []; // Reset mảng upload
                 // Cần gọi lại để refresh existingImages sau khi thêm ảnh mới
                 $this->product->load('images');
                 $this->existingImages = $this->product->images->toArray();
            }

            DB::commit();

            session()->flash('success', $this->productId ? 'Cập nhật sản phẩm thành công!' : 'Thêm sản phẩm thành công!');
            return redirect()->route('seller.products.index');

        } catch (\Exception $e) {
            DB::rollBack();
            // Log lỗi
            \Log::error('Save product error: ' . $e->getMessage());
            // Có thể thêm $dispatch để báo lỗi chi tiết hơn
            session()->flash('error', 'Đã xảy ra lỗi khi lưu sản phẩm. Vui lòng thử lại.');
        }
    }

    public function render()
    {
        return view('seller.products.product-form')
               ->layout('layouts.SellerDashBoard'); // <-- Đặt layout ở đây
    }
}