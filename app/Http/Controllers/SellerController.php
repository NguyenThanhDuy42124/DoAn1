<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Log;


class SellerController extends Controller
{   
   public function index()
{
    // Lấy top 4 cửa hàng uy tín (seller)
    $shops = User::where('role', 'seller')->take(4)->get();

    // Lấy top 8 sản phẩm nổi bật (có thể dựa theo lượt mua hoặc rating)
    $products = Product::with('seller')
                ->orderByDesc('created_at')
                ->take(8)
                ->get();

    return view('MainPage', compact('shops', 'products'));
}
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

    // Tính counts riêng
    $pendingCount = Order::where('seller_id', $seller->id)->where('status', 'Pending')->count();
    $shippedCount = Order::where('seller_id', $seller->id)->where('status', 'Shipped')->count();
    $deliveredCount = Order::where('seller_id', $seller->id)->where('status', 'Delivered')->count();
    $completedCount = Order::where('seller_id', $seller->id)->where('status', 'Completed')->count();

    $orders = Order::where('seller_id', $seller->id)
        ->with('buyer', 'items.product')
        ->when($status, function($query, $status) {
            return $query->where('status', $status);
        })
        ->orderBy('id', 'ASC')
        ->paginate(10);

    return view('seller.orders.index', compact('orders', 'status', 'pendingCount', 'shippedCount', 'deliveredCount', 'completedCount'));
}


    public function show($id)
    {
        $order = Order::where('seller_id', Auth::id())->with('items.product')->findOrFail($id);
        return view('seller.orders.show', compact('order'));
    }


    public function bulkApprove(Request $request)
    {
        $currentStatusQuery = $request->query('status', 'Pending');
        $request->validate(['order_ids' => 'required|array']);

        $seller = Auth::user();
        $orderIds = $request->order_ids;

        $orders = Order::where('seller_id', $seller->id)->whereIn('id', $orderIds)->get();
        foreach($orders as $order) {
            if($order->status === 'Pending') {
                $order->status = 'Shipped';
                $order->save();
                Notification::create([
                    'user_id' => $order->user_id,
                    'type' => 'order_status_updated',
                    'message' => "Đơn hàng #{$order->id} của bạn đã được xác nhận và đang được vận chuyển",
                    'is_read' => false,
                ]);
            }
        }
        return redirect()->route('seller.orders.index', ['status' => $currentStatusQuery])->with('success', 'Đã phê duyệt đơn hàng thành công!');
    }


    public function updateStatus(Request $request, $id)
{
    Log::info('UpdateStatus called', ['id' => $id, 'user_id' => Auth::id(), 'input_status' => $request->input('status'), 'query_status' => $request->query('status')]);

    $order = Order::where('seller_id', Auth::id())->findOrFail($id);
    
    Log::info('Order found', ['order_id' => $order->id, 'current_status' => $order->status, 'payment_status' => $order->payment_status]); // Log data order

    $newStatus = $request->input('status');
    $currentStatusQuery = $request->query('status', 'Pending');
      $oldStatus = $order->status; 
    if ($newStatus === 'Shipped' && $order->status === 'Pending') {
        $order->status = 'Shipped';
    } elseif ($newStatus === 'Delivered' && $order->status === 'Shipped') {
        $order->status = 'Delivered';
    } else {
        Log::warning('Invalid status transition', ['order_id' => $id, 'from' => $order->status, 'to' => $newStatus]);
        return redirect()->route('seller.orders.index', ['status' => $currentStatusQuery])->with('error', 'Không thể cập nhật trạng thái.');
    }

    try {
        $order->save();
        Log::info('Order updated successfully', ['order_id' => $id, 'new_status' => $order->status]);
    } catch (\Exception $e) {
        Log::error('Save failed', ['order_id' => $id, 'error' => $e->getMessage()]);
        return redirect()->route('seller.orders.index', ['status' => $currentStatusQuery])->with('error', 'Lỗi lưu DB: ' . $e->getMessage());
    }

    return redirect()->route('seller.orders.index', ['status' => $currentStatusQuery])->with('success', 'Cập nhật trạng thái thành công.');
}



}