<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckAdminRole
{
    /**
     * Handle an incoming request.
     * Allow only users with role 'admin', 'staff' or 'inventory'.
     */
    public function handle(Request $request, Closure $next)
    {
        // Prefer admin session (set by admin login), else check auth user
        $user = null;
        if ($request->session()->has('admin_user_id')) {
            $user = \App\Models\User::find($request->session()->get('admin_user_id'));
        } else {
            $user = auth()->user();
        }

        if (!$user) {
            // Log reason for redirect so we can debug unexpected logouts
            Log::warning('CheckAdminRole: no admin user found for request', [
                'path' => $request->path(),
                'session_admin_user_id' => $request->session()->get('admin_user_id'),
                'auth_user_id' => optional(auth()->user())->id,
            ]);
            return redirect()->route('admin.login');
        }

        if (!in_array($user->role, ['admin', 'staff', 'inventory'])) {
            // Log unauthorized role attempts
            Log::warning('CheckAdminRole: user has unauthorized role', [
                'path' => $request->path(),
                'user_id' => $user->id ?? null,
                'role' => $user->role ?? null,
            ]);
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
