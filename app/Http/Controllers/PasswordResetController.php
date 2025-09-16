<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class PasswordResetController extends Controller
{
    public function showReset_Password(Request $request,string $token) : View
        {
            return view('auth.PasswordReset',
            ['token' => $token,
            'email' => $request->query('email')]);
        }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:3|confirmed',
        ],[],[
            'email' => 'Email',
            'password' => 'Mật khẩu',
        ]);

        $status = Password::reset(
            $request->only('email','password','password_confirmation','token'),
            function (User $user, string $password) {
                $user->forceFill(attributes: [
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ]);
                $user -> save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

}
