<?php

namespace App\Http\Controllers;


use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Product;     // nếu có model
use App\Models\Category;    // nếu cần load danh mục
use App\Models\Brand; // *** THÊM MỚI ***
use App\Models\ProductAttributeValue; // *** THÊM MỚI ***
use App\Models\Attribute; // *** THÊM MỚI ***


class ProductController extends Controller
{
    public function create()
    {
        // *** CẬP NHẬT: Load thêm Brands ***
        $categories = Category::whereDoesntHave('children') // Chỉ lấy category KHÔNG CÓ con
                      ->orderBy('name') // Sắp xếp theo tên cho dễ nhìn
                      ->get();
        $brands = Brand::orderBy('name')->get(); // Lấy danh sách thương hiệu
        
        // Mày sẽ cần một view phức tạp hơn (tốt nhất là Livewire)
        // để load thuộc tính động khi chọn category.
        // Tạm thời, ta chỉ truyền 2 cái này.
        return view('seller.products.create_product', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        // *** CẬP NHẬT: Sửa validation cho brand_id và thêm 'attributes' ***
        $validated = $request->validate([
            'seller_id'   => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'brand_id'    => 'nullable|exists:brands,id', // <-- Đổi từ 'brand'
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric',
            'stock'       => 'nullable|integer',
            'description' => 'nullable|string',
            'status'      => 'nullable|string',
            'images.*'     => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'attributes'  => 'nullable|array', // <-- Thêm cho EAV
            'attributes.*' => 'nullable|string|max:255', // Giá trị của EAV
        ]);

        DB::beginTransaction();
        try {
            // 2. *** CẬP NHẬT: Dùng brand_id ***
            $product = Product::create([
                'seller_id'   => $request->input('seller_id'),
                'category_id' => $request->input('category_id'),
                'brand_id'    => $request->input('brand_id'), // <-- Đổi
                'name'        => $request->input('name'),
                'price'       => $request->input('price'),
                'stock'       => $request->input('stock'),
                'description' => $request->input('description'),
            ]);

            // 3. Xử lý tải lên và lưu hình ảnh (Giữ nguyên)
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('product_images', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                    ]);
                }
            }
            
            // 4. *** THÊM MỚI: Xử lý lưu thuộc tính EAV ***
            if ($request->has('attributes')) {
                foreach ($request->input('attributes') as $attribute_id => $value) {
                    if (!empty($value)) { // Chỉ lưu nếu có giá trị
                        ProductAttributeValue::create([
                            'product_id' => $product->id,
                            'attribute_id' => $attribute_id,
                            'value' => $value,
                        ]);
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Store Product Error: " . $e->getMessage()); // Ghi log
            return redirect()->back()->withErrors('Có lỗi xảy ra, vui lòng thử lại.');
        }

        return redirect()->route('seller.products.index')
            ->with('success', 'Thêm sản phẩm thành công!');
    }

    public function index(Request $request) // (Seller Dashboard)
    {
        $query = Product::with('images', 'category') // Thêm category
                        ->where('seller_id', auth()->id());

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        
        if ($request->filled('category')) {
            $query->where('category_id', $request->input('category'));
        }
        
        // *** THÊM MỚI: Lọc theo brand_id ***
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->input('brand_id'));
        }

        // *** CẬP NHẬT: Lấy categories và brands cho bộ lọc ***
        $categories = Category::whereDoesntHave('children') // Chỉ lấy category KHÔNG CÓ con
                      ->orderBy('name') // Sắp xếp theo tên cho dễ nhìn
                      ->get();
        $brands = Brand::orderBy('name')->get(); // Lấy từ bảng brands

        $products = $query->orderBy('created_at', 'desc')
                         ->paginate(99) 
                         ->withQueryString(); 

        // *** CẬP NHẬT: Trả về view, thêm $brands ***
        return view('seller.products.index', compact('products', 'categories', 'brands'));
    }

    public function showImportForm()
    {
        $categories = Category::whereDoesntHave('children') // Chỉ lấy category KHÔNG CÓ con
                      ->orderBy('name') // Sắp xếp theo tên cho dễ nhìn
                      ->get();
        return view('seller.products.Import', compact('categories'));
    }

    // Phương thức xử lý Import (Bước 4)
    public function import(Request $request)
    {
        // 1. Validation
        $request->validate([
            'category_id' => 'required|exists:categories,id', // Đảm bảo category_id hợp lệ
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
            'images.*'   => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        $categoryId = $request->input('category_id');
        $sellerId = Auth::id(); // Lấy ID của người bán đang đăng nhập
        $filePath = $request->file('excel_file')->path();

        // *** THÊM MỚI: Lấy danh sách thuộc tính của danh mục này ***
        $categoryAttributes = Attribute::whereHas('categories', function($q) use ($categoryId) {
            $q->where('category_id', $categoryId);
        })->get();

        // Tạo Collection các file ảnh đã tải lên, dùng tên file làm key
        $uploadedImages = collect($request->file('images'))->keyBy(function($file) {
            return $file->getClientOriginalName();
        });

        DB::beginTransaction();
        $importedCount = 0;

        try {
            // 2. Import dữ liệu với fast-excel
            (new FastExcel())
                ->import($filePath, function ($row) use ($categoryId, $sellerId, $uploadedImages, &$importedCount, $categoryAttributes) {

                    // --- ÁNH XẠ DỮ LIỆU SẢN PHẨM TỪ EXCEL VÀ FORM ---

                    // Kiểm tra dữ liệu cơ bản từ Excel
                    if (empty($row['name']) || empty($row['price'])) {
                         return null; // Bỏ qua dòng thiếu dữ liệu bắt buộc
                    }

                    // *** CẬP NHẬT: Xử lý Brand Name từ Excel ***
                    $brand_id = null;
                    if (!empty($row['brand'])) {
                        // Tìm hoặc Tạo Mới Brand và lấy ID
                        $brand = Brand::firstOrCreate(['name' => trim($row['brand'])]);
                        $brand_id = $brand->id;
                    }

                    $product = Product::create([
                        'seller_id'   => $sellerId,
                        'category_id' => $categoryId, // LẤY TỪ FORM
                        'brand_id'    => $brand_id, // <-- Dùng brand_id
                        'name'        => $row['name'],
                        'price'       => (float)($row['price']),
                        'stock'       => (int)($row['stock'] ?? 0),
                        'description' => $row['description'] ?? null,
                    ]);

                    //  --- XỬ LÝ HÌNH ẢNH  ---

                    // *** THÊM MỚI: Xử lý nhập thuộc tính EAV từ Excel ***
                    foreach ($categoryAttributes as $attribute) {
                        $columnName = $attribute->name; // Tên cột trong Excel (VD: "RAM", "CPU")
                        
                        // Kiểm tra xem cột có tồn tại và có giá trị không
                        if (isset($row[$columnName]) && !empty($row[$columnName])) {
                            ProductAttributeValue::create([
                                'product_id' => $product->id,
                                'attribute_id' => $attribute->id,
                                'value' => $row[$columnName],
                            ]);
                        }
                    }


                    // Chỉ chạy logic xử lý ảnh NẾU CÓ BẤT KỲ FILE NÀO ĐƯỢC UPLOAD.
                    // Nếu không có ảnh nào được upload, $uploadedImages là Collection rỗng,
                    // code sẽ bỏ qua toàn bộ khối này.
                    /** @phpstan-ignore-next-line */
                    if ($uploadedImages->isNotEmpty()) {
                        // Tìm tất cả các cột ảnh trong dòng hiện tại
                        $imageColumns = array_filter($row, function($key) {
                            return str_contains(strtolower($key), 'image_');
                        }, ARRAY_FILTER_USE_KEY);

                        foreach ($imageColumns as $imageName) {
                            $imageName = trim($imageName);
                            // Kiểm tra tên file ảnh có trong danh sách ảnh đã upload không
                            if ($imageName && $uploadedImages->has($imageName)) {
                                $imageFile = $uploadedImages->get($imageName);

                                // Lưu file ảnh vào storage và tạo bản ghi DB
                                $path = $imageFile->store('product_images', 'public');

                                ProductImage::create([
                                    'product_id' => $product->id,
                                    'image_path' => $path,
                                ]);
                            }
                        }
                    }
                    //  KẾT THÚC KHỐI XỬ LÝ ẢNH
                    $importedCount++;
                    return $product;
                });

            DB::commit();

            return redirect()->back()->with('success', 'Đã nhập thành công ' . $importedCount . ' sản phẩm.');

        } catch (\Exception $e) {
            DB::rollBack();
            // Ghi log lỗi để dễ dàng debug
            Log::error("Import Error: " . $e->getMessage() . " on line " . $e->getLine());

            return redirect()->back()->with('error', 'Lỗi nhập dữ liệu: Đã xảy ra lỗi nghiêm trọng. Vui lòng kiểm tra file Excel và Log hệ thống.');
        }
    }
   // Mở file: ProductController.php
    // THAY THẾ TOÀN BỘ HÀM listProducts CŨ BẰNG HÀM NÀY

    // Mở file: ProductController.php
    // THAY THẾ TOÀN BỘ HÀM listProducts CŨ BẰNG HÀM NÀY

    // Mở file: app/Http/Controllers/ProductController.php
    // Đảm bảo hàm listProducts của bạn giống như sau:

    public function listProducts(Request $request)
    {
        // 1. Bắt đầu query
        $query = Product::with([
                'images', 'seller', 'category', 
                'brand', 'attributeValues.attribute'
            ]) 
            ->where('status', Product::STATUS_APPROVED);

        // 2. Lọc theo Khoảng giá (Giống hệt logic form)
        if ($request->filled('price_range')) {
            $range = $request->input('price_range');
            $parts = explode('-', $range); 
            $minPrice = $parts[0];
            $maxPrice = $parts[1] ?? null; 

            if ($minPrice > 0) $query->where('price', '>=', $minPrice);
            // Sửa logic: nếu maxPrice rỗng (ví dụ: "20000000-") thì không lọc max
            if ($maxPrice !== null && $maxPrice > 0 && $maxPrice > $minPrice) {
                $query->where('price', '<=', $maxPrice);
            }
        }

        // 3. Lọc theo Thương hiệu (Giống hệt logic form, dùng ID)
        if ($request->filled('brand')) {
            $query->where('brand_id', $request->input('brand'));
        }

        // 4. Lọc "Còn hàng" (Giống hệt logic form)
        if ($request->filled('in_stock')) {
            $query->where('stock', '>', 0);
        }

        // 5. Lọc theo Danh mục (Giống hệt logic form, dùng ID cha)
        if ($request->filled('category')) {
            $categoryId = $request->input('category');
            $category = Category::with('children')->find($categoryId);
            if ($category) {
                // Lấy ID cha và TẤT CẢ ID con
                $allCategoryIds = $category->children->pluck('id')->push($category->id)->all();
                $query->whereIn('category_id', $allCategoryIds);
            }
        }

        // 6. Lấy dữ liệu cho dropdown filter
        $brands = Brand::orderBy('name')->get();
        // Lấy danh mục cha (để khớp với @foreach trong form)
        $categories = Category::whereNull('parent_id')->orderBy('name')->get();

        // 7. Thực thi query và phân trang
        $products = $query->latest() 
                           ->paginate(12) 
                           // Rất quan trọng: Giữ lại filter khi chuyển trang
                           ->withQueryString(); 

        // 8. Trả về view với đầy đủ dữ liệu
        return view('pages.listproducts', compact('products', 'brands', 'categories'));
    }
    public function showProductDetail($id)
    {
        // Chỉ đơn thuần trả về view và truyền $id.
        // Toàn bộ dữ liệu tĩnh sẽ được xử lý trong file Blade.
        return view('pages.product-detail', ['id' => $id]);
    }
    public function destroy($id)
    {
        // Tìm sản phẩm theo id
        $product = Product::findOrFail($id);

        // Xóa sản phẩm
        $product->delete();

        // Điều hướng về danh sách sản phẩm với thông báo
        return redirect()->route('seller.products.index')
            ->with('success', 'Xóa sản phẩm thành công!');
    }
    public function edit($id)
    {
        // *** CẬP NHẬT: Load tất cả dữ liệu cần thiết ***
        $categories = Category::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();
        
        // Load sản phẩm KÈM các giá trị thuộc tính hiện có
        $product = Product::with('attributeValues.attribute')->findOrFail($id);

        // Load tất cả thuộc tính (và options) mà danh mục này YÊU CẦU
        // (Để render ra form cho đúng)
        $categoryAttributes = Attribute::whereHas('categories', function($q) use ($product) {
            $q->where('category_id', $product->category_id);
        })->with('options')->get(); // Load kèm options cho 'select'

        return view('seller.products.edit_product', compact(
            'product', 
            'categories', 
            'brands', 
            'categoryAttributes'
        ));
    }

    public function update(Request $request, $id)
    {
        // *** CẬP NHẬT: Validation ***
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'brand_id'    => 'nullable|exists:brands,id', // <-- Đổi
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'description' => 'nullable|string',
            'status'      => 'required|string', // Mày có thể validate 'in:...'
            'images.*'    => 'nullable|image|max:2048',
            'deleted_images' => 'nullable|array',
            'deleted_images.*' => 'exists:product_images,id',
            'attributes'  => 'nullable|array', // <-- Thêm cho EAV
            'attributes.*' => 'nullable|string|max:255',
        ]);

        $product = Product::findOrFail($id);

        DB::beginTransaction();
        try {
            // 2. *** CẬP NHẬT: Dùng brand_id ***
            $product->update([
                'category_id' => $request->category_id,
                'brand_id'    => $request->brand_id, // <-- Đổi
                'name'        => $request->name,
                'price'       => $request->price,
                'stock'       => $request->stock,
                'description' => $request->description,
                'status'      => $request->status,
            ]);
            // 3. Xóa các hình ảnh đã chọn (nếu có)
            if ($request->has('deleted_images')) {
                $deletedImageIds = $request->input('deleted_images');
                $imagesToDelete = ProductImage::whereIn('id', $deletedImageIds)->where('product_id', $product->id)->get();

                foreach ($imagesToDelete as $image) {
                    // Xóa file vật lý khỏi storage
                    Storage::disk('public')->delete($image->image_path);
                    // Xóa bản ghi trong database
                    $image->delete();
                }
            }

            // 4. Thêm các hình ảnh mới (nếu có)
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('product_images', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                    ]);
                }
            }

            $product->attributeValues()->delete(); // Xóa hết
            
            if ($request->has('attributes')) {
                foreach ($request->input('attributes') as $attribute_id => $value) {
                    if (!empty($value)) { // Chỉ lưu nếu có giá trị
                        ProductAttributeValue::create([
                            'product_id' => $product->id,
                            'attribute_id' => $attribute_id,
                            'value' => $value,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('seller.products.index')->with('success', 'Sản phẩm đã được cập nhật thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Đã xảy ra lỗi khi cập nhật sản phẩm. Vui lòng thử lại.')->withInput();
        }
    }



    // ... các method index, show, edit, update, destroy nếu cần
}
