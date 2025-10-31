<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB; // <-- Thêm dòng này
use Carbon\Carbon; // <-- Thêm dòng này
use App\Models\Brand;



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
    // Logic của bạn: chỉ cần payment_status = 'paid'
    $todayRevenue = Order::where('seller_id',  Auth::id())
                             ->where('payment_status', 'paid')
                             ->whereDate('created_at', Carbon::today()) // Chỉ lấy các đơn trong hôm nay
                             ->sum('total_price');
    $todayOrderCount = Order::where('seller_id', Auth::id())
                            ->whereDate('created_at', Carbon::today()) // Dựa trên ngày tạo
                            ->count();      
    $latestOrders = Order::where('seller_id', Auth::id())
                         ->orderByDesc('created_at') // Sắp xếp mới nhất lên đầu
                         ->take(5) // Chỉ lấy 5 đơn
                         ->get();                                           
    // Trả về view, thêm 'pendingProductCount' vào compact
    return view('seller.dashboard', compact('products','pendingProductCount','approvedProductCount','todayRevenue','todayOrderCount','latestOrders'));
}
public function showShop(Request $request, $id)
    {
        // 1. Lấy thông tin cửa hàng
        $shop = User::where('role', 'seller')->findOrFail($id);
        $shopRating = $shop->sellerReviews()->avg('rating');
        $shopReviewCount = $shop->sellerReviews()->count();
        // 2. Lấy tham số 'sort' và 'category' từ URL
        $sort = $request->query('sort', 'newest');
        $selectedCategory = $request->query('category'); // <-- THÊM MỚI
        
        $isFollowing = false;
        $followerCount = $shop->followers()->count(); // Đếm số người theo dõi

        if (Auth::check()) {
            // Kiểm tra xem user hiện tại có đang theo dõi shop này không
            $isFollowing = Auth::user()->following()->where('seller_id', $shop->id)->exists();
        }
        // 3. THÊM MỚI: Lấy tất cả danh mục để hiển thị ở sidebar
        // (Giả sử bạn có model App\Models\Category)
        $categories = Category::orderBy('name')
                              ->get();

        // 4. Xây dựng câu truy vấn sản phẩm
        $productQuery = Product::with(['images', 'category', 'brand']) 
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
        $totalProductCount = Product::where('seller_id', $shop->id)
                                    ->where('status', 'Approved')
                                    ->count(); // <-- Dùng count()
        // 7. Lấy kết quả (phân trang)
        // Dùng appends() để giữ nguyên tham số ?sort=... và ?category=...
        $products = $productQuery->paginate(12)->appends($request->query());

        // 8. Trả về view, truyền thêm $categories và $selectedCategory
        return view('pages.shop', compact(
            'shop', 
            'products', 
            'sort', 
            'categories', 
            'selectedCategory',
            'totalProductCount',
            'shopRating',      
            'shopReviewCount',
            'isFollowing',    
            'followerCount'   
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

// ... (Hàm showReportPage() của bạn ở đây) ...

    /**
     * Cung cấp dữ liệu doanh thu cho API (Biểu đồ 2)
     * Chấp nhận tham số: ?range=7d, ?range=1m, ?range=1y
     */
    public function getRevenueReport(Request $request)
{
    $sellerId = Auth::id();

    // --- KIỂM TRA REQUEST MỚI (TỪ TRANG BÁO CÁO) ---
    if ($request->has('range')) {
        
        $range = $request->input('range', '7d');
        $labels = [];
        $values = [];
        $query = Order::where('seller_id', $sellerId)
                        ->where('payment_status', 'paid');

        switch ($range) {
            case '1y':
                // --- 1 NĂM (12 tháng qua, nhóm theo tháng) ---
                $startDate = Carbon::now()->subMonths(11)->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                
                $dbData = $query->whereBetween('created_at', [$startDate, $endDate]) // <-- ĐÃ SỬA
                    ->select(
                        DB::raw('SUM(total_price) as revenue'),
                        DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month") // <-- ĐÃ SỬA
                    )
                    ->groupBy('month')->orderBy('month', 'ASC')->pluck('revenue', 'month');

                // Lặp 12 tháng để lấp đầy dữ liệu
                for ($i = 0; $i < 12; $i++) {
                    $date = Carbon::now()->subMonths(11 - $i);
                    $labelFormat = $date->format('Y-m'); // "2025-10"
                    $labels[] = $date->format('m/Y');    // "10/2025"
                    $values[] = $dbData->get($labelFormat, 0);
                }
                break;

            case '1m':
                // --- 1 THÁNG (30 ngày qua, nhóm theo ngày) ---
                $startDate = Carbon::now()->subDays(29)->startOfDay();
                $endDate = Carbon::now()->endOfDay();

                $dbData = $query->whereBetween('created_at', [$startDate, $endDate]) // <-- ĐÃ SỬA
                    ->select(
                        DB::raw('SUM(total_price) as revenue'),
                        DB::raw("DATE(created_at) as date") // <-- ĐÃ SỬA
                    )
                    ->groupBy('date')->orderBy('date', 'ASC')->pluck('revenue', 'date');

                // Lặp 30 ngày để lấp đầy dữ liệu
                for ($i = 0; $i < 30; $i++) {
                    $date = Carbon::now()->subDays(29 - $i);
                    $labelFormat = $date->format('Y-m-d'); // "2025-10-25"
                    $labels[] = $date->format('d/m');    // "25/10"
                    $values[] = $dbData->get($labelFormat, 0);
                }
                break;
            
            case '7d':
            default:
                // --- 7 NGÀY (7 ngày qua, nhóm theo ngày) ---
                $startDate = Carbon::now()->subDays(6)->startOfDay();
                $endDate = Carbon::now()->endOfDay();

                $dbData = $query->whereBetween('created_at', [$startDate, $endDate]) // <-- ĐÃ SỬA
                    ->select(
                        DB::raw('SUM(total_price) as revenue'),
                        DB::raw("DATE(created_at) as date") // <-- ĐÃ SỬA
                    )
                    ->groupBy('date')->orderBy('date', 'ASC')->pluck('revenue', 'date');

                // Lặp 7 ngày để lấp đầy dữ liệu
                for ($i = 0; $i < 7; $i++) {
                    $date = Carbon::now()->subDays(6 - $i);
                    $labelFormat = $date->format('Y-m-d');
                    $labels[] = $date->format('d/m');
                    $values[] = $dbData->get($labelFormat, 0);
                }
                break;
        }

        // Trả về JSON KIỂU MỚI cho trang Báo cáo
        return response()->json(['labels' => $labels, 'values' => $values]);

    } 
    
    // --- REQUEST CŨ (TỪ TRANG TỔNG QUAN) ---
    else {
        
        // Đây là logic 7 ngày GỐC của bạn
        $salesData = Order::where('seller_id', $sellerId)
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', Carbon::now()->subDays(6)->startOfDay()) // <-- ĐÃ SỬA
            ->select(
                DB::raw('DATE(created_at) as date'), // <-- ĐÃ SỬA
                DB::raw('SUM(total_price) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get()
            ->pluck('revenue', 'date');

        $reportData = [];
        $startDate = Carbon::now()->subDays(6);

        for ($i = 0; $i < 7; $i++) {
            $date = $startDate->copy()->addDays($i)->format('Y-m-d');
            $reportData[] = [
                'date' => $date,
                'revenue' => $salesData->get($date, 0)
            ];
        }

        // Trả về JSON KIỂU CŨ cho trang Tổng quan
        return response()->json($reportData);
    }
}
  
    public function showReportPage()
    {
        $sellerId = Auth::id();

        // 1. LẤY DỮ LIỆU TOP 5 KHÁCH HÀNG (THEO DOANH THU)
        // Truy vấn này đơn giản hơn, chỉ cần query bảng 'orders'
        $topCustomersData = Order::where('seller_id', $sellerId)
            ->where('payment_status', 'paid') //
            ->select(
                'buyer_name', //
                DB::raw('SUM(total_price) as total_spent') //
            )
            ->groupBy('buyer_name')
            ->orderByDesc('total_spent') // Sắp xếp theo tổng chi tiêu
            ->take(5) // Lấy 5 người cao nhất
            ->get();
        
        // 2. Xử lý dữ liệu cho Chart.js
        $topCustomerLabels = $topCustomersData->pluck('buyer_name');
        $topCustomerValues = $topCustomersData->pluck('total_spent');

        // 3. Trả về view
        return view('seller.reports.index', compact('topCustomerLabels', 'topCustomerValues'));
    }

}