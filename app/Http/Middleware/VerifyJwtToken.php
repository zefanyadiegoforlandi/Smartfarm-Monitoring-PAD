<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyJwtToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = session('jwt') ?? $request->cookie('jwt');
        if (!$token) {
            return redirect('/')->withErrors('Token tidak ditemukan. Sesi berakhir, silakan login terlebih dahulu.');
        }

        return $next($request);
    }
}
