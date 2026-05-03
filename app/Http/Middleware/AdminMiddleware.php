<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            // Pakai redirectGuest agar tidak menyimpan intended URL
            // yang bisa menyebabkan redirect loop
            return redirect()->route('admin.login')->withHeaders([
                'Cache-Control' => 'no-store, no-cache',
            ]);
        }

        return $next($request);
    }
}
