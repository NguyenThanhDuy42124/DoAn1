<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         $user = $request->user();


        // Nếu tài khoản bị khóa / chưa kích hoạt
        if ($user->status !== 'active') {
            Log::warning('Tài khoản của bạn chưa được kích hoạt hoặc đã bị khóa.');
            return redirect()->route('login')->with('error', 'Tài khoản của bạn chưa được kích hoạt hoặc đã bị khóa.');
        }

        return $next($request); // Cho phép đi tiếp nếu hợp lệ
    }
}
