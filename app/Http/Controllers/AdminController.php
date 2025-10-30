<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB; 
class AdminController extends Controller
{
        // hàm này để load trang dashboard của admin, có thêm phần tìm kiếm user
    public function dashboard(Request $request)
    {
        $role = session('current_role', Auth::user()->role);

        // Lấy danh sách user nếu là admin
        $users = collect(); // mặc định trống

        $query = User::where('role', '!=', 'admin');

        // Nếu có từ khóa tìm kiếm
        if ($request->filled('keyword')) {
            $query->where('name', 'like', '%' . $request->keyword . '%');
        }

        $users = $query->paginate(10)->appends($request->query());
        // --- THÊM LOGIC ĐẾM ĐƠN HÀNG ---
    $totalOrders = Order::count(); //
    $totalProducts = Product::count();
    // (Giả định role của người mua là 'buyer' và người bán là 'seller')
    $sellerCount = User::where('role', 'seller')->count();
    $buyerCount = User::where('role', 'buyer')->count();
    
    // Chuẩn bị dữ liệu cho Chart.js
    $userChartData = [
        'labels' => ['Người bán (Seller)', 'Người mua (Buyer)'],
        'values' => [$sellerCount, $buyerCount]
    ];
    $productStatusCounts = Product::select('status', DB::raw('count(*) as count'))
                                ->groupBy('status')
                                ->pluck('count', 'status'); // -> ['Pending' => 50, 'Approved' => 200, ...]

    // Định nghĩa các trạng thái bạn muốn hiển thị (dựa trên Model Product)
    $statuses = [
        Product::STATUS_PENDING => 'Đang chờ duyệt',
        Product::STATUS_APPROVED => 'Đã duyệt',
        Product::STATUS_REJECTED => 'Bị từ chối',
        Product::STATUS_HIDDEN => 'Bị ẩn'
    ];

    $productStatusLabels = [];
    $productStatusValues = [];

    foreach ($statuses as $statusCode => $statusName) {
        $productStatusLabels[] = $statusName;
        $productStatusValues[] = $productStatusCounts->get($statusCode, 0); // Lấy count, mặc định là 0
    }
    
    $productStatusData = [
        'labels' => $productStatusLabels,
        'values' => $productStatusValues
    ];
        // Trả view dashboard, luôn truyền $users
        return view('admin.dashboard', compact('users', 'role', 'totalOrders',
        'totalProducts', 
        'userChartData',
        'productStatusData'));
    }
    public function userDashboard(Request $request){
        $role = session('current_role', Auth::user()->role);

        // Lấy danh sách user nếu là admin
        $users = collect(); // mặc định trống

        $query = User::where('role', '!=', 'admin');

        // Nếu có từ khóa tìm kiếm
        if ($request->filled('keyword')) {
            $query->where('name', 'like', '%' . $request->keyword . '%');
        }

        $users = $query->paginate(10)->appends($request->query());


        // Trả view dashboard, luôn truyền $users
        return view('admin.Users.UserManager', compact('users', 'role'));
    }
    public function destroy(User $user)
    {
        $message = 'Cook 1 tài khoản thành công';
        $user->delete();
        return redirect()->route('admin.users.manager')->with('success', 'User deleted successfully.')->with('message', $message);
    }
    // hàm này để load trang edit user
    public function edit($id)
    {
        $user = User::findOrFail($id);
        if (!$user) {
            return redirect()->route('admin.dashboard')->with('error', 'User not found.');
        }
        return view('admin.edit', compact('user'));
    }
    // hàm này để cập nhật user
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
    if ($request->has('delete_image') && $user->img) {
        Storage::delete('public/' . $user->img);
        $user->img = null;
    }


    $data = $request->only(['role','status','img']);



    $user->update($data);

    return redirect()->route('admin.users.manager')->with('message', 'Cập nhật thành công');
    }

    // hàm này để load trang tạo user
    public function create()
    {
        return view('admin.create');
    }
    // tạo tài khoản
    public function store(Request $request)
    {
        $message = 'tạo 1 tài khoản thành công';
        $incomingData = $request->validate([
            "email" => "required|email|max:255|unique:users,email",
            "name" => "required|string|max:255|unique:users,name",
            "password" => "required|string|min:3",
        ]);
        $incomingData["password"] = bcrypt($incomingData["password"]);
        User::create($incomingData);
        return redirect()->route('admin.users.manager')->with('message', $message);
    }
}
