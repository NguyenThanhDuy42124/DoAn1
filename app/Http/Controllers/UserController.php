<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
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
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
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
public function create()
{
 return view('admin.create');
}
public function edit($id)
{
    $user = User::findOrFail($id);
    if (!$user) {
        return redirect()->route('admin.dashboard')->with('error', 'User not found.');
    }
    return view('admin.edit', compact('user'));
}

public function update(Request $request, $id)
{
    $user = User::findOrFail($id);
    if (!$user) {
        return redirect()->route('admin.dashboard')->with('error', 'User not found.');
    }
    $user->password = bcrypt($request->password);
    $user->update($request->all());
    $message = 'Cập nhật tài khoản thành công';
    return redirect()->route('admin.dashboard')->with('message', $message);
}




public function store(Request $request)
{
    $message ='tạo 1 tài khoản thành công';
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
