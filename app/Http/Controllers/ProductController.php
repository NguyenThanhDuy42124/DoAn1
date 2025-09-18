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
    ]);

    Product::create($validated);

    // Nếu đang trong admin

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
{    $categories = Category::orderBy('name')->get();
    $product = Product::findOrFail($id);
    return view('seller.products.edit_product', compact('product','categories'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|numeric',
        'brand' => 'nullable|string|max:255',
        'stock' => 'required|integer|min:0',
        'description' => 'nullable|string',
        'status' => 'required|in:active,inactive', 
        'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
    ]);

    $product = Product::findOrFail($id);

    // Gán dữ liệu
    $product->name = $request->name;
    $product->price = $request->price;
    $product->brand = $request->brand;
    $product->stock = $request->stock;
    $product->description = $request->description;
    $product->status = $request->status; 

    // Xử lý ảnh (nếu có upload mới)
    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('products', 'public');
        $product->image = $path;
    }

    $product->save();

    return redirect()->route('seller.products.index')->with('success', 'Cập nhật sản phẩm thành công!');
}

    // ... các method index, show, edit, update, destroy nếu cần
}
