<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class DebugWebhookRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('webhook') || $request->is('api/webhook')) {
            Log::info('Webhook hit: Method=' . $request->method() . ' | Headers: ' . json_encode($request->headers->all()));
        }
        $response = $next($request);
        if ($response->isRedirect()) {
            Log::error('Redirect 302 for webhook: Location=' . $response->headers->get('Location'));
        }
        return $response;
    }
}
