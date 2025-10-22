<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class BuyerController extends Controller
{
    public function orders(Request $request)
    {
        $user = Auth::user();
        $status = $request->query('status', 'Pending');
        $orders = Order::where('user_id', $user->id)
        ->with('items.product', 'seller')
        ->when($status, function ($query, $status)
        {
            return $query->where('status', $status);
        })
        ->latest()
        ->paginate(10);
        return view('buyer.orders.index', compact('orders', 'status'));
    }

    public function returnOrder($id)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);
        if ($order->status === 'Delivered')
        {
            $order->status = 'Returned';
            $order->save();
            // logic trả hàng
        }
        return redirect()->route('buyer.orders.index')->with('success', 'Yêu cầu trả hàng đã được gửi.');
    }

}
