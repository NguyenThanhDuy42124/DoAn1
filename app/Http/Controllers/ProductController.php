<?php

namespace App\Http\Controllers;

// *** SỬA: Xóa ProductAttributeValue ***
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
// use App\Models\ProductAttributeValue; // <-- *** XÓA ***
use App\Models\Attribute; // <-- Vẫn giữ (cho "Khuôn Mẫu" import)


class ProductController extends Controller
{
    public function create()
    {
        // *** SỬA: Đơn giản hóa query Category ***
        $categories = Category::orderBy('name')->get(); // Không cần 'whereDoesntHave' nữa
        $brands = Brand::orderBy('name')->get();

        // View này có lẽ không còn dùng nếu mày dùng Livewire Full-page component
        // Nhưng nếu dùng, nó vẫn chạy
        return view('seller.products.create_product', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        // *** SỬA: Validation cho 'attributes' ***
        $validated = $request->validate([
            'seller_id'   => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'brand_id'    => 'nullable|exists:brands,id',
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric',
            'stock'       => 'nullable|integer',
            'description' => 'nullable|string',
            'status'      => 'nullable|string',
            'images.*'     => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'attributes'  => 'nullable|array', // <-- Giữ nguyên, Model sẽ cast sang JSON
        ]);

        DB::beginTransaction();
        try {
            // *** SỬA: Thêm 'attributes' vào create() ***
            $product = Product::create([
                'seller_id'   => $request->input('seller_id'),
                'category_id' => $request->input('category_id'),
                'brand_id'    => $request->input('brand_id'),
                'name'        => $request->input('name'),
                'price'       => $request->input('price'),
                'stock'       => $request->input('stock'),
                'description' => $request->input('description'),
                // Gán trực tiếp mảng attributes, Model sẽ tự cast sang JSON
                'attributes'  => $request->input('attributes', []), // <-- THÊM MỚI
            ]);

            // Xử lý tải lên và lưu hình ảnh (Giữ nguyên)
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('product_images', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                    ]);
                }
            }


            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Store Product Error: " . $e->getMessage());
            return redirect()->back()->withErrors('Có lỗi xảy ra, vui lòng thử lại.');
        }

        return redirect()->route('seller.products.index')
            ->with('success', 'Thêm sản phẩm thành công!');
    }

    public function index(Request $request) // (Seller Dashboard)
    {
        // Query cơ bản (Giữ nguyên)
        $query = Product::with('images', 'category')
            ->where('seller_id', auth()->id());

        // Các filter (Giữ nguyên)
        if ($request->filled('search')) {
            // Tách chuỗi tìm kiếm thành các từ khóa
            $searchTerm = trim($request->input('search'));
            $keywords = explode(' ', $searchTerm);

            // Thêm điều kiện AND cho mỗi từ khóa,
            // dùng LOWER() để không phân biệt hoa/thường
            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $word) {
                    if (!empty($word)) {
                        $q->whereRaw('LOWER(name) LIKE ?', ['%' . mb_strtolower($word, 'UTF-8') . '%']);
                    }
                }
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->input('category'));
        }
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->input('brand_id'));
        }

        // *** SỬA: Đơn giản hóa query Category ***
        $categories = Category::orderBy('name')->get(); // Không cần 'whereDoesntHave'
        $brands = Brand::orderBy('name')->get();

        $products = $query->orderBy('created_at', 'desc')
            ->paginate(99)
            ->withQueryString();

        return view('seller.products.index', compact('products', 'categories', 'brands'));
    }

    public function showImportForm()
    {
        // *** SỬA: Đơn giản hóa query Category ***
        $categories = Category::orderBy('name')->get(); // Không cần 'whereDoesntHave'
        return view('seller.products.Import', compact('categories'));
    }

    // Phương thức xử lý Import (Bước 4)
    public function import(Request $request)
    {
        // 1. Validation (Giữ nguyên)
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
            'images.*'   => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        $categoryId = $request->input('category_id');
        $sellerId = Auth::id();
        $filePath = $request->file('excel_file')->path();

        // *** THÊM MỚI: Lấy "Khuôn Mẫu" thuộc tính của danh mục này ***
        // (Logic này vẫn đúng)
        $categoryAttributes = Attribute::whereHas('categories', function ($q) use ($categoryId) {
            $q->where('category_id', $categoryId);
        })->get();

        // Tạo mảng chỉ chứa TÊN thuộc tính (RAM, CPU,...)
        $attributeColumnNames = $categoryAttributes->pluck('name')->all();

        // Xử lý ảnh (Giữ nguyên)
        $uploadedImages = collect($request->file('images'))->keyBy(function ($file) {
            return $file->getClientOriginalName();
        });

        DB::beginTransaction();
        $importedCount = 0;

        try {
            (new FastExcel())
                ->import($filePath, function ($row) use (
                    $categoryId,
                    $sellerId,
                    $uploadedImages,
                    &$importedCount,
                    $categoryAttributes,
                    $attributeColumnNames
                ) {

                    if (empty($row['name']) || empty($row['price'])) {
                        return null;
                    }

                    // Xử lý Brand (Giữ nguyên)
                    $brand_id = null;
                    if (!empty($row['brand'])) {
                        $brand = Brand::firstOrCreate(['name' => trim($row['brand'])]);
                        $brand_id = $brand->id;
                    }

                    // *** THÊM MỚI: Tách dữ liệu tĩnh và dữ liệu động (JSON) ***
                    $staticData = [
                        'seller_id'   => $sellerId,
                        'category_id' => $categoryId,
                        'brand_id'    => $brand_id,
                        'name'        => $row['name'],
                        'price'       => (float)($row['price']),
                        'stock'       => (int)($row['stock'] ?? 0),
                        'description' => $row['description'] ?? null,
                    ];

                    // Lọc mảng $row, chỉ lấy các cột có tên nằm trong "Khuôn Mẫu"
                    $attributesData = [];
                    foreach ($attributeColumnNames as $columnName) {
                        if (isset($row[$columnName]) && !empty($row[$columnName])) {
                            $attributesData[$columnName] = $row[$columnName];
                        }
                    }

                    // *** SỬA: Tạo sản phẩm với cột 'attributes' (JSON) ***
                    $product = Product::create($staticData + [
                        'attributes' => $attributesData // Gán mảng thuộc tính vào đây
                    ]);

                    // *** XÓA: Bỏ toàn bộ khối xử lý EAV cũ ***
                    // foreach ($categoryAttributes as $attribute) { ... }

                    // Xử lý hình ảnh (Giữ nguyên)
                    if ($uploadedImages->isNotEmpty()) {
                        // Tìm tất cả các cột ảnh trong dòng hiện tại
                        $imageColumns = array_filter($row, function ($key) {
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

                    $importedCount++;
                    return $product;
                });

            DB::commit();

            return redirect()->back()->with('success', 'Đã nhập thành công ' . $importedCount . ' sản phẩm.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Import Error: " . $e->getMessage() . " on line " . $e->getLine());
            return redirect()->back()->with('error', 'Lỗi nhập dữ liệu: Đã xảy ra lỗi. Vui lòng kiểm tra file Excel và Log.');
        }
    }

    public function listProducts(Request $request)
    {
        // 1. Bắt đầu query
        // *** SỬA: Xóa 'attributeValues.attribute' vì không cần join EAV nữa ***
        $query = Product::with(['images', 'seller', 'category', 'brand'])
            ->where('status', Product::STATUS_APPROVED);
        if ($request->filled('search')) {
            // Tách chuỗi tìm kiếm thành các từ khóa
            $searchTerm = trim($request->input('search'));
            $keywords = explode(' ', $searchTerm);

            // Thêm điều kiện AND cho mỗi từ khóa,
            // dùng LOWER() để không phân biệt hoa/thường
            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $word) {
                    if (!empty($word)) {
                        $q->whereRaw('LOWER(name) LIKE ?', ['%' . mb_strtolower($word, 'UTF-8') . '%']);
                    }
                }
            });
        }
        // 2. Lọc (Giữ nguyên)
        if ($request->filled('price_range')) {
            $range = $request->input('price_range');
            $parts = explode('-', $range);
            $minPrice = $parts[0];
            $maxPrice = $parts[1] ?? null;

            if ($minPrice > 0) {
                $query->where('price', '>=', $minPrice);
            }
            if ($maxPrice !== null && $maxPrice > 0) {
                $query->where('price', '<=', $maxPrice);
            }
        }
        if ($request->filled('brand')) {
            $query->where('brand_id', $request->input('brand'));
        }
        if ($request->filled('in_stock')) {
            $query->where('stock', '>', 0);
        }

        // 3. *** SỬA: Đơn giản hóa lọc Category ***
        if ($request->filled('category')) {
            $categoryId = $request->input('category');
            // Vì category là phẳng, không cần tìm con
            $query->where('category_id', $categoryId);
        }

        // 4. Lấy dữ liệu cho dropdown filter
        $brands = Brand::orderBy('name')->get();
        // *** SỬA: Đơn giản hóa query Category ***
        $categories = Category::orderBy('name')->get(); // Không cần logic cây

        // 5. Thực thi query (Giữ nguyên)
        $products = $query->latest()
            ->paginate(12)
            ->withQueryString();

        return view('pages.listproducts', compact('products', 'brands', 'categories'));
    }

    public function showProductDetail($id)
    {
        // 1. Tải sản phẩm chính VÀ đếm/tính trung bình reviews
        // (Tôi thêm 'brand' và 'category' để hiển thị đầy đủ thông tin ở view)

        $product = Product::with(['images', 'brand', 'category'])
            ->withCount('reviews') // <-- Tự động tạo biến 'reviews_count'
            ->withAvg('reviews', 'rating') // <-- Tự động tạo biến 'reviews_avg_rating'
            ->findOrFail($id);
        // Các trạng thái cần bảo mật
        $protectedStatuses = [
            Product::STATUS_PENDING ?? 'pending',
            Product::STATUS_REJECTED ?? 'rejected',
            Product::STATUS_HIDDEN ?? 'hidden',
        ];

        // Nếu sản phẩm ở trạng thái "bảo mật" (pending/rejected/hidden)
        if (in_array($product->status, $protectedStatuses, true)) {
            $user = auth()->user();

            // Nếu chưa đăng nhập -> chặn (hoặc chuyển sang 404 để che thông tin)
            if (!$user) {
                abort(403, 'Bạn cần có quyền hạn để xem sản phẩm này.');
                // hoặc: abort(404); // ít lộ thông tin hơn
            }

            // Chỉ admin hoặc chính seller mới được phép
            if ($user->role !== 'admin' && $user->id !== $product->seller_id) {
                abort(403, 'Bạn không có quyền truy cập sản phẩm này.');
                // hoặc: abort(404);
            }
        }

        // 2. Lấy thông tin seller (Giữ nguyên)
        $seller = $product->seller;

        // 3. Tải các đánh giá (có phân trang)
        // Sắp xếp mới nhất, và tải kèm thông tin người mua (buyer)
        $reviews = $product->reviews()
            ->with('buyer')
            ->latest()
            ->paginate(5, ['*'], 'reviews_page'); // Phân trang 5 review/trang

        // 4. Tải sản phẩm liên quan (cùng danh mục)
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            // Bổ sung điều kiện giống như hàm listProducts
            ->where('status', Product::STATUS_APPROVED)
            // Bổ sung eager load 'images' để tối ưu view (tránh N+1)
            ->with('images')
            ->latest()
            ->take(5) // Lấy 5 sản phẩm
            ->get();

        // 5. Trả về view và truyền TẤT CẢ các biến
        return view('pages.product-detail', compact(
            'product',
            'seller',
            'reviews', // <-- BIẾN MỚI
            'relatedProducts' // <-- BIẾN MỚI
        ));
    }



    public function destroy($id)
    {
        // (Giữ nguyên, không thay đổi)
        $product = Product::findOrFail($id);
        $product->delete();
        return redirect()->route('seller.products.index')
            ->with('success', 'Xóa sản phẩm thành công!');
    }

    public function edit($id)
    {
        // *** SỬA: Đơn giản hóa query ***
        $categories = Category::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();

        // Load sản phẩm. Không cần 'with('attributeValues')'
        // vì $product->attributes đã là mảng (nhờ $casts)
        $product = Product::findOrFail($id);

        // Load "Khuôn Mẫu" thuộc tính (Logic này vẫn đúng và cần thiết)
        $categoryAttributes = Attribute::whereHas('categories', function ($q) use ($product) {
            $q->where('category_id', $product->category_id);
        })->with('options')->get();

        return view('seller.products.edit_product', compact(
            'product',
            'categories',
            'brands',
            'categoryAttributes'
        ));
    }

    public function update(Request $request, $id)
    {
        // *** SỬA: Validation ***
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'brand_id'    => 'nullable|exists:brands,id',
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'images.*'    => 'nullable|image|max:2048|mimes:jpeg,png,jpg,gif,svg', // Đổi 'image' thành 'images.*'
            'deleted_images' => 'nullable|array',
            'deleted_images.*' => 'exists:product_images,id', // Đảm bảo ID hình ảnh tồn tại
            'attributes'  => 'nullable|array', // <-- Giữ nguyên
        ]);

        $product = Product::findOrFail($id);

        DB::beginTransaction();
        try {
            // *** SỬA: Gộp 'attributes' vào update() ***
            $product->update([
                'category_id' => $request->category_id,
                'brand_id'    => $request->brand_id,
                'name'        => $request->name,
                'price'       => $request->price,
                'stock'       => $request->stock,
                'description' => $request->description,
                'status'      => $request->status,
                'images.*'    => 'nullable|image|max:2048|mimes:jpeg,png,jpg,gif,svg', // Đổi 'image' thành 'images.*'
                'deleted_images' => 'nullable|array',
                'deleted_images.*' => 'exists:product_images,id', // Đảm bảo ID hình ảnh tồn tại
                'attributes'  => $request->input('attributes', []), // <-- THÊM MỚI
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


            DB::commit();

            return redirect()->route('seller.products.index')->with('success', 'Sản phẩm đã được cập nhật thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Update Product Error: " . $e->getMessage()); // Ghi log
            return redirect()->back()->with('error', 'Đã xảy ra lỗi khi cập nhật.')->withInput();
        }
    }
}
