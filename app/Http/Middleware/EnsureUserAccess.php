<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserAccess
{
    public function handle(Request $request, Closure $next)
    {
        $level = session('user_level');

        if ($level !== 'user') {
            return redirect('/pages/dashboard/admin-dashboard')->withErrors('Access denied. This area is only for user.');
        }

        return $next($request);
    }
}
