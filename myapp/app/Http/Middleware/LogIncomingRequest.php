<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogIncomingRequest
{
    /**
     * Handle an incoming request.
     * This is temporary debug middleware to log cookies and CSRF header.
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $cookies = $request->header('Cookie') ?: null;
            $csrf = $request->header('X-CSRF-TOKEN') ?: null;
            $path = $request->path();
            $method = $request->method();
            $sessionId = $request->session()->getId();

            Log::info('Incoming request debug', [
                'path' => $path,
                'method' => $method,
                'cookie_header' => $cookies,
                'x_csrf_header' => $csrf,
                'session_id' => $sessionId,
                'remote_addr' => $request->ip(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to log incoming request debug: ' . $e->getMessage());
        }

        return $next($request);
    }
}
