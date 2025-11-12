<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->role !== 'admin') {
            abort(403, 'Only admin can perform this action.');
        }

        return $next($request);
    }
}
