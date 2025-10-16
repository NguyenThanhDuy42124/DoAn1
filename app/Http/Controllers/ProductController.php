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

class ProductController extends Controller
{
    public function create()
    {
        // nếu cần truyền dữ liệu như danh mục
        $categories = Category::all();
        return view('seller.products.create_product', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'seller_id'   => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric',
            'brand'       => 'nullable|string|max:255',
            'stock'       => 'nullable|integer',
            'description' => 'nullable|string',
            'status'      => 'nullable|string',
            'image.*'       => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);
        // Sử dụng Transaction để đảm bảo tính toàn vẹn dữ liệu
        DB::beginTransaction();
        try {
            // 2. Tạo sản phẩm mới và lưu vào bảng products
            $product = Product::create([
                'seller_id'   => $request->input('seller_id'),
                'category_id' => $request->input('category_id'),
                'name'        => $request->input('name'),
                'price'       => $request->input('price'),
                'brand'       => $request->input('brand'),
                'stock'       => $request->input('stock'),
                'description' => $request->input('description'),
            ]);

            // 3. Xử lý tải lên và lưu hình ảnh
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('product_images', 'public');

                    // Lưu đường dẫn hình ảnh cùng với product_id vừa tạo
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                    ]);
                }
            }

            // Commit transaction nếu mọi thứ thành công
            DB::commit();
        } catch (\Exception $e) {
            // Rollback transaction nếu có lỗi xảy ra
            DB::rollBack();
            return redirect()->back()->withErrors('Có lỗi xảy ra, vui lòng thử lại.');
        }

        // Nếu đang trong seller
        return redirect()->route('seller.products.index')
            ->with('success', 'Thêm sản phẩm thành công!');
    }
    public function index()
    {
        // Lấy sản phẩm của người bán hiện tại và tải kèm hình ảnh của chúng.
        $products = Product::with('images')
            ->where('seller_id', auth()->id())
            ->paginate(8);

        return view('seller.products.index', compact('products'));
    }
// Phương thức hiển thị form (Bước 3)
    public function showImportForm()
    {
        // Lấy danh sách danh mục để hiển thị trong select box
        $categories = Category::all();
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

        // Tạo Collection các file ảnh đã tải lên, dùng tên file làm key
        $uploadedImages = collect($request->file('images'))->keyBy(function($file) {
            return $file->getClientOriginalName();
        });

        DB::beginTransaction();
        $importedCount = 0;

        try {
            // 2. Import dữ liệu với fast-excel
            (new FastExcel())
                ->import($filePath, function ($row) use ($categoryId, $sellerId, $uploadedImages, &$importedCount) {

                    // --- ÁNH XẠ DỮ LIỆU SẢN PHẨM TỪ EXCEL VÀ FORM ---

                    // Kiểm tra dữ liệu cơ bản từ Excel
                    if (empty($row['name']) || empty($row['price'])) {
                         return null; // Bỏ qua dòng thiếu dữ liệu bắt buộc
                    }

                    $product = Product::create([
                        'seller_id'   => $sellerId,
                        'category_id' => $categoryId, // LẤY TỪ FORM
                        'name'        => $row['name'],
                        'price'       => (float)($row['price']),
                        'brand'       => $row['brand'] ?? null,
                        'stock'       => (int)($row['stock'] ?? 0),
                        'description' => $row['description'] ?? null,
                    ]);

                    //  --- XỬ LÝ HÌNH ẢNH  ---

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
    public function listProducts()
    {
        // Lấy tất cả sản phẩm và tải kèm hình ảnh, sau đó phân trang.
        $products = Product::with('images')->paginate(9);

        return view('pages.listproducts', compact('products'));
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
        $categories = Category::orderBy('name')->get();
        $product = Product::findOrFail($id);
        return view('seller.products.edit_product', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        // 1. Validate dữ liệu sản phẩm và hình ảnh mới
        $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'brand'       => 'nullable|string|max:255',
            'stock'       => 'required|integer|min:0',
            'description' => 'nullable|string',
            'status'      => 'required|in:active,inactive',
            'images.*'    => 'nullable|image|max:2048|mimes:jpeg,png,jpg,gif,svg', // Đổi 'image' thành 'images.*'
            'deleted_images' => 'nullable|array',
            'deleted_images.*' => 'exists:product_images,id', // Đảm bảo ID hình ảnh tồn tại
        ]);

        $product = Product::findOrFail($id);

        DB::beginTransaction();
        try {
            // 2. Cập nhật thông tin sản phẩm
            $product->update([
                'name'        => $request->name,
                'price'       => $request->price,
                'brand'       => $request->brand,
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

            DB::commit();

            return redirect()->route('seller.products.edit', $product->id)->with('success', 'Sản phẩm đã được cập nhật thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Đã xảy ra lỗi khi cập nhật sản phẩm. Vui lòng thử lại.')->withInput();
        }
    }



    // ... các method index, show, edit, update, destroy nếu cần
}
