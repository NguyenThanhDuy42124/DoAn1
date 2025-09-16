<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;     // nếu có model
use App\Models\Category;    // nếu cần load danh mục

class ProductController extends Controller
{
    public function create()
    {
        // nếu cần truyền dữ liệu như danh mục
        $categories = Category::all();
        return view('seller.create_product', compact('categories'));
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
    ]);

    Product::create($validated);

    // Nếu đang trong admin

// Nếu đang trong seller
return redirect()->route('seller.products.index')
                 ->with('success', 'Thêm sản phẩm thành công!');

}
    public function index()
    {
        // Lấy tất cả sản phẩm từ DB
        $products = Product::all();

        // Trả về view (ví dụ: resources/views/products/index.blade.php)
        return view('pages.listproducts', compact('products'));
    }

    // ... các method index, show, edit, update, destroy nếu cần
}
