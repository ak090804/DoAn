<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
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

        if ($user->role !== 'admin') {
            abort(403, 'Only admin can perform this action.');
        }

        return $next($request);
    }
}
