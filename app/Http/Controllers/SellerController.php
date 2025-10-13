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

    public function orders(Request $request)
    {
        $seller = Auth::user(); 
        $status = $request->query('status', 'Pending');
        $orders = Order::where('seller_id', $seller->id)
            ->with('buyer', 'items.product')
            ->when($status, function($query, $status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate(10);
        return view('seller.orders.index', compact('orders', 'status'));
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


    public function updateStatus(Request $request, $id)
    {
        $order = Order::where('seller_id', Auth::id())->findOrFail($id);
        $newStatus = $request->input('status');
        if($newStatus === 'Shipped' && $order->status === 'Pending')
        {
            $order->status = 'Shipped';
        } 
        elseif ($newStatus === 'Delivered' && $order->status === 'Shipped')
        {
            $order->status = 'Delivered';
        } else
        {
            return redirect()->route('seller.orders.index')->with('error', 'Không thể cập nhật trạng thái. Vui lòng kiểm tra trạng thái hiện tại.');
        }
        $order->save();
        return redirect()->route('seller.orders.index')->with('success', 'Cập nhật trạng thái thành công.');
    }



}