<?php

namespace App\Http\Controllers;

use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
        $products = Product::where('seller_id', auth()->id())->paginate(8);
        return view('seller.products.index', compact('products'));
    }

    public function listProducts()
    {
        $products = Product::paginate(9);
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

            return redirect()->route('seller.products.index', $product->id)->with('success', 'Sản phẩm đã được cập nhật thành công.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Đã xảy ra lỗi khi cập nhật sản phẩm. Vui lòng thử lại.')->withInput();
        }
    }



    // ... các method index, show, edit, update, destroy nếu cần
}
