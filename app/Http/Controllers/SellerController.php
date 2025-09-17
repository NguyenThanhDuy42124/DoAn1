<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class SellerController extends Controller
{
    public function dashboard()
    {
        // Lấy sản phẩm của seller hiện tại
        $products = Product::where('seller_id', Auth::id())->get();
        
        return view('seller.dashboard', compact('products'));
    }
}