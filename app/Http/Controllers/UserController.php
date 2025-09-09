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
            "name" => "required|string|max:255|unique:users,name",
            "password" => "required|string|min:3",
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
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required'],
        ]);

        if (Auth::attempt(['name' => $incomingData['name'], 'password' => $incomingData['password']])) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'login' => 'Tên hoặc mật khẩu không đúng.',
        ])->onlyInput('name');
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


        // Trả view dashboard, luôn truyền $users
        return view('admin.dashboard', compact('users', 'role'));
    }
    public function destroy(User $user)
    {
        $message = 'Cook 1 tài khoản thành công';
        $user->delete();
        return redirect()->route('admin.dashboard')->with('success', 'User deleted successfully.')->with('message', $message);
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

    $data = $request->only(['name', 'email', 'password', 'role']);

    // Nếu password không nhập lại thì bỏ qua
    if (empty($data['password'])) {
        unset($data['password']);
    } else {
        $data['password'] = Hash::make($data['password']);
    }

    $user->update($data);

    return redirect()->route('admin.dashboard')->with('message', 'Cập nhật thành công');
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
        return redirect()->route('admin.dashboard')->with('message', $message);
    }
    
}
