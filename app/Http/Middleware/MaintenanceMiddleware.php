<?php

namespace App\Http\Middleware;

use App\Models\SystemConfig;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Admin yang sudah login — selalu lolos
        if (Auth::check() && Auth::user()->is_admin) {
            return $next($request);
        }

        // 2. Semua route /admin/* — selalu lolos (login page, dll)
        if ($request->segment(1) === 'admin') {
            return $next($request);
        }

        // 3. Cek maintenance mode
        if (SystemConfig::getValue('maintenance_mode') === '1') {
            return response()->view('maintenance', [
                'appName' => SystemConfig::getValue('app_name', 'DOMPETKU'),
            ], 503);
        }

        return $next($request);
    }
}
