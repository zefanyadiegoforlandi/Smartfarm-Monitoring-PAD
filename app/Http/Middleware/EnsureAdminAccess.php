<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAdminAccess
{
    public function handle(Request $request, Closure $next)
    {
        $level = session('user_level');

        if ($level !== 'admin') {
            return redirect('/pages/dashboard/user-dashboard')->withErrors('Access denied. This area is only for administrators.');
        }

        return $next($request);
    }
}
