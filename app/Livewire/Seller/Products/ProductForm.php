<?php

namespace App\Livewire\Seller\Products;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Attribute;
// use App\Models\ProductAttributeValue; // *** XÓA: Không cần model này nữa ***
use App\Models\ProductImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Rule;
use Illuminate\Support\Collection;

class ProductForm extends Component
{
    use WithFileUploads;

    // --- Thuộc tính chính ---
    public ?Product $product; 
    public $productId = null; 

    // --- Dữ liệu Form (bind với wire:model) ---
    #[Rule('required|exists:categories,id')]
    public $category_id = ''; 

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

    // *** THAY ĐỔI: Mảng này giờ sẽ được map vào cột JSON ***
    // (Ta vẫn dùng key là attribute_id cho dễ bind với view)
    // Ví dụ: [ 1 => '16GB', 2 => 'Đen' ]
    public $attributeValues = [];

    // --- Ảnh sản phẩm (Giữ nguyên) ---
    public $images = []; 
    public $existingImages = []; 
    public $deletedImageIds = []; 

    // --- Dữ liệu phụ trợ (load 1 lần) ---
    public Collection $allCategories; 
    public Collection $allBrands;     
    public Collection $categoryAttributes; // "Khuôn Mẫu" thuộc tính

    /**
     * Khởi chạy component (Xử lý cả Create và Edit)
     */
   public function mount($productId = null)
    {
        // *** SỬA: Đơn giản hóa query Category ***
        $this->allCategories = Category::orderBy('name')->get();
        
        $this->allBrands = Brand::orderBy('name')->get();
        $this->categoryAttributes = collect();

        if ($productId) {
            // *** SỬA: Bỏ 'attributeValues' ra khỏi query ***
            $productModel = Product::with(['images'])->findOrFail($productId);

            $this->product = $productModel;
            $this->productId = $productModel->id;

            // Fill dữ liệu tĩnh (Giữ nguyên)
            $this->category_id = $productModel->category_id;
            $this->name = $productModel->name;
            $this->price = $productModel->price;
            $this->brand_id = $productModel->brand_id;
            $this->stock = $productModel->stock;
            $this->description = $productModel->description;
            $this->existingImages = $productModel->images->toArray();

            // Load "Khuôn Mẫu" (Logic này vẫn đúng)
            $this->loadCategoryAttributes(); 

            // *** SỬA: Đọc thuộc tính từ cột JSON (Model đã cast sang array) ***
            $this->attributeValues = $productModel->attributes ?? [];

        } else { // Nếu là Create
            $this->product = new Product(); 
        }
    }

    /**
     * Chạy khi $category_id thay đổi (Giữ nguyên)
     */
    public function updatedCategoryId($value)
    {
        $this->loadCategoryAttributes();
        $this->attributeValues = [];
    }

    /**
     * Helper: Tải "Khuôn Mẫu" thuộc tính (Giữ nguyên)
     */
    public function loadCategoryAttributes()
    {
        if (!empty($this->category_id)) {
            // Logic này VẪN ĐÚNG, vì nó đọc từ bảng "Khuôn Mẫu"
            $this->categoryAttributes = Attribute::whereHas('categories', function($q) {
                $q->where('category_id', $this->category_id);
            })->with('options') 
              ->orderBy('name')
              ->get();
        } else {
            $this->categoryAttributes = collect(); 
        }
    }

     /**
      * Rules (Giữ nguyên)
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
            'attributeValues'  => 'nullable|array', // Vẫn là array
            'attributeValues.*'=> 'nullable|string|max:255',
            'images.*'    => 'nullable|image|max:2048',
         ];
     }

    // --- Xử lý ảnh (Giữ nguyên) ---
    public function removeExistingImage($imageId)
    {
        $this->deletedImageIds[] = $imageId; 
        $this->existingImages = array_filter($this->existingImages, fn($img) => $img['id'] != $imageId);
    }
     public function removeNewImage($index)
     {
         array_splice($this->images, $index, 1);
     }


    /**
     * Lưu sản phẩm (Create hoặc Update)
     */
    public function save()
    {
        // 1. Xóa dd() test đi
        // dd($this->images); 
        $validatedData = $this->validate();

        DB::beginTransaction();
        try {
            // --- 1. Lưu/Cập nhật thông tin cơ bản ---
            $productData = [
                'category_id' => $validatedData['category_id'],
                'brand_id'    => $validatedData['brand_id'] ?: null, 
                'name'        => $validatedData['name'],
                'price'       => $validatedData['price'],
                'stock'       => $validatedData['stock'],
                'description' => $validatedData['description'],
                'seller_id'   => Auth::id(), 
                'status'      => $this->product->exists ? $this->product->status : Product::STATUS_PENDING,
                'attributes'  => $validatedData['attributeValues'] ?? [],
            ];

            if ($this->productId) { // Update
                $this->product->update($productData);
            } else { // Create
                $this->product = Product::create($productData);
                // Cập nhật lại $this->productId phòng khi cần dùng ngay sau đó
                $this->productId = $this->product->id; 
            }

            // --- 2. Xử lý ảnh CŨ cần xóa (ĐÃ ĐIỀN CODE) ---
            if (!empty($this->deletedImageIds)) {
                $imagesToDelete = ProductImage::whereIn('id', $this->deletedImageIds)
                                               ->where('product_id', $this->product->id) // Bảo mật: chỉ xóa ảnh của sp này
                                               ->get();
                
                foreach ($imagesToDelete as $image) {
                    Storage::disk('public')->delete($image->image_path); // Xóa file
                    $image->delete(); // Xóa record DB
                }
                
                $this->deletedImageIds = []; // Reset mảng
            }

            // --- 3. Xử lý ảnh MỚI upload (ĐÃ ĐIỀN CODE) ---
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
                
                 // Reset mảng upload
                 $this->images = []; 
                 // Tải lại ảnh (quan trọng)
                 $this->product->load('images'); 
                 $this->existingImages = $this->product->images->toArray();
            }

            DB::commit();

            session()->flash('success', $this->productId ? 'Cập nhật sản phẩm thành công!' : 'Thêm sản phẩm thành công!');
            // Sửa lại: redirect() phải là cái cuối cùng
            return redirect()->route('seller.products.index');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Save product error: ' . $e->getMessage() . ' on line ' . $e->getLine()); // Thêm getLine()
            session()->flash('error', 'Đã xảy ra lỗi khi lưu sản phẩm. Vui lòng thử lại.');
        }
    }

    public function render()
    {
        // *** SỬA: Đổi tên view cho chuẩn convention (hoặc giữ tên cũ của mày) ***
        // Tao giả sử file view của mày nằm ở 'resources/views/livewire/seller/products/product-form.blade.php'
        // Nếu file của mày là 'seller/products/product-form.blade.php' thì đổi lại thành:
        // return view('seller.products.product-form')
        return view('seller.products.product-form') 
               ->layout('layouts.SellerDashBoard');
    }
}