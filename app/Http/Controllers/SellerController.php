<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\Log;


class SellerController extends Controller
{   
   public function index()
{
    // Lấy top 4 cửa hàng uy tín (seller)
    $shops = User::where('role', 'seller')->take(4)->get();

    // Lấy sản phẩm (đã lọc status)
    // Tải kèm 'seller' và 'images' để dùng ở view
    $products = Product::with(['seller', 'images']) 
                       ->where('status', 'Approved') // Chỉ lấy sản phẩm đã duyệt
                       ->orderByDesc('created_at')
                       ->paginate(12); // Phân trang

    // Trả về view, chỉ truyền 'shops' và 'products'
    return view('MainPage', compact('shops', 'products'));
}
    public function dashboard()
{
    // Lấy sản phẩm của seller hiện tại (bạn có thể giữ lại nếu cần dùng)
    $products = Product::where('seller_id', Auth::id())->get();
    // Đếm số lượng sản phẩm đang bán (Approved)
    $approvedProductCount = Product::where('seller_id',Auth::id())
                                   ->where('status', 'Approved') //
                                   ->count();
    // THÊM MỚI: Đếm số lượng sản phẩm đang chờ duyệt
    $pendingProductCount = Product::where('seller_id', Auth::id())
                                  ->where('status', 'Pending') //
                                  ->count();
    
    // Trả về view, thêm 'pendingProductCount' vào compact
    return view('seller.dashboard', compact('products', 'pendingProductCount','approvedProductCount'));
}
public function showShop(Request $request, $id)
    {
        // 1. Lấy thông tin cửa hàng
        $shop = User::where('role', 'seller')->findOrFail($id);

        // 2. Lấy tham số 'sort' và 'category' từ URL
        $sort = $request->query('sort', 'newest');
        $selectedCategory = $request->query('category'); // <-- THÊM MỚI

        // 3. THÊM MỚI: Lấy tất cả danh mục để hiển thị ở sidebar
        // (Giả sử bạn có model App\Models\Category)
        $categories = Category::all();

        // 4. Xây dựng câu truy vấn sản phẩm
        $productQuery = Product::with(['images', 'category']) 
                               ->where('seller_id', $shop->id)
                               ->where('status', 'Approved');

        // 5. THÊM MỚI: Lọc theo danh mục nếu được chọn
        if ($selectedCategory) {
            $productQuery->where('category_id', $selectedCategory); //
        }

        // 6. Áp dụng logic sắp xếp
        switch ($sort) {
            case 'price_asc':
                $productQuery->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $productQuery->orderBy('price', 'desc');
                break;
            case 'newest':
            default:
                $productQuery->orderByDesc('created_at');
                break;
        }

        // 7. Lấy kết quả (phân trang)
        // Dùng appends() để giữ nguyên tham số ?sort=... và ?category=...
        $products = $productQuery->paginate(12)->appends($request->query());

        // 8. Trả về view, truyền thêm $categories và $selectedCategory
        return view('pages.shop', compact(
            'shop', 
            'products', 
            'sort', 
            'categories', 
            'selectedCategory'
        ));
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