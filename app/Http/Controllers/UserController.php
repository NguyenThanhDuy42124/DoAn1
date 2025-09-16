<?php

namespace App\Http\Controllers;

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
        ]);
        $incomingData["password"] = bcrypt($incomingData["password"]);
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

        if (Auth::attempt(['email' => $incomingData['email'], 'password' => $incomingData['password']])) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'login' => 'Tên hoặc mật khẩu không đúng.',
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




}
