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
}
