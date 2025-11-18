<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // hàm này để đăng ký
    public function register(Request $request)
    {
        $incomingData = $request->validate([
            "email" => "required|email|max:255|unique:users,email",
            "name" => "required|string|max:255",
            "password" => "required|string|min:3",
            "phoneNumber" => "nullable|string|max:15",
            "dateOfBirth" => "nullable|date",
            "gender" => "nullable|string|in:male,female,other",
            "address" => "nullable|string|max:255",
        ]);
        $incomingData['img'] = null; // default null for profile image
        $incomingData["password"] = bcrypt($incomingData["password"]);
        $incomingData["status"] = "active";
        $user = User::create($incomingData);
        Auth::login($user);
        return redirect('/dashboard');
    }
    // hàm này để đăng xuất
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
    // hàm này để đăng nhập
    public function login(Request $request)
    {
        $incomingData = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required'],
        ]);

        if (Auth::attempt([
            'email' => $incomingData['email'],
            'password' => $incomingData['password'],
        ])) {
            $user = Auth::user();
            if ($user->status === 'inactive') {
                Auth::logout();
                return back()->withErrors([
                    'login' => 'Tài khoản của bạn đã bị vô hiệu hóa. Vui lòng liên hệ quản trị viên để biết thêm chi tiết.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'login' => 'Email hoặc mật khẩu không chính xác.',
        ])->onlyInput('email');
    }

    // hàm này để load trang dashboard của seller và admin
    public function index()
    {
        $role = session('current_role', Auth::user()->role);

        if ($role === 'admin') {
            return view('admin.dashboard');
        } elseif ($role === 'seller') {
            return view('seller.dashboard');
        }
        return view('dashboard');
    }
    // chuyển đổi role giữa buyer sang seller hoặc Admin
    public function switchRole($role)
    {
        $user = Auth::user();

        if ($user->role == $role) {
            session(['current_role' => $role]);
            return redirect()->route($role . '.dashboard');
        }

        abort(403, 'Không có quyền');
    }
    public function requestToBecomeSeller(Request $request)
    {
        $user = Auth::user();

        if ($user->ekyc_status !== 'verified') {
            return redirect()->route('general.users.edit', $user->id)
                ->with('error', 'Bạn cần hoàn thành eKYC và được xác minh trước khi gửi yêu cầu trở thành người bán.');
        }
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $user->id,
                'type' => 'Buyer_Request',
                'message' => 'Bạn đã gửi yêu cầu trở thành người bán. với ID: ' . $user->id . ' Vui lòng chờ xác minh từ quản trị viên.',
            ]);
        }

        return redirect()->back()->with('message', 'Yêu cầu của bạn đã được gửi thành công. Vui lòng chờ xét duyệt từ quản trị viên.');
    }
}
