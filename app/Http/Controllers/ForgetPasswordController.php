<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ForgetPasswordController extends Controller
{
     public function showForget_Password(Request $request) : View
        {
            return view('auth.ForgotPassword');
        }

    public function sendResetLink(Request $request)
        {
            $request->validate(['email' => 'required|email']);

            $status = Password::sendResetLink($request->only('email'));
            $generic = 'Đã gửi liên kết mật khẩu, xin hãy kiểm tra email của bạn!';

            // Throttle notification
            if (in_array($status, [
                Password::RESET_LINK_SENT,
                Password::INVALID_USER,
                Password::RESET_THROTTLED,
            ], true)) {
                return back()->with('status', $generic);
            }
            return back()->withErrors(['email' => 'Hiện không thể gửi email, xin vui lòng hãy chờ đợi!']);
        }

}
