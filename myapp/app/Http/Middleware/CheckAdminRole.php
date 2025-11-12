<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

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
            return redirect()->route('admin.login');
        }

        if (!in_array($user->role, ['admin', 'staff', 'inventory'])) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
