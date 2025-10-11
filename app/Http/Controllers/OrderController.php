<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function purchaseHistory()
    {
        $user = Auth::user();
        
        $orders = Order::where('user_id', $user->id) // Sử dụng user_id thay vì buyer_id
            ->with('items.product')
            ->orderBy('created_at', 'desc')
            ->get();

        $ordersGrouped = $orders->groupBy(function ($order) {
            return $order->session_id . '-' . $order->seller_id;
        });
        
        return view('buyer.checkouts.purchase_history', compact('ordersGrouped'));
    }
}
