<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class SellerController extends Controller
{
    public function dashboard()
    {
        // Lấy sản phẩm của seller hiện tại
        $products = Product::where('seller_id', Auth::id())->get();
        
        return view('seller.dashboard', compact('products'));
    }

    public function orders()
    {
        $seller = Auth::user(); 
        $orders = Order::where('seller_id', $seller->id)
            ->with('buyer', 'items.product')
            ->latest()
            ->paginate(10);
        return view('seller.orders.index', compact('orders'));
    }


    public function show($id)
    {
        $order = Order::where('seller_id', Auth::id())->with('items.product')->findOrFail($id);
        return view('seller.orders.show', compact('order'));
    }


    public function bulkApprove(Request $request)
    {
        $request->validate(['order_ids' => 'required|array']);

        $seller = Auth::user();
        $orderIds = $request->order_ids;

        $orders = Order::where('seller_id', $seller->id)->whereIn('id', $orderIds)->get();
        foreach($orders as $order) {
            if($order->status === 'Pending') {
                $order->status = 'Shipped';
                $order->save();
            }
        }
        return redirect()->route('seller.orders.index')->with('success', 'Đã phê duyệt đơn hàng thành công!');
        

    }
}