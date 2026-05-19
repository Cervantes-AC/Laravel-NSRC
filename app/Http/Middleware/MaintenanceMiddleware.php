<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Setting::getValue('maintenance_mode', false)) {
            $message = Setting::getValue('maintenance_message', 'System is undergoing scheduled maintenance. Please check back shortly.');

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                    'maintenance' => true,
                ], 503);
            }

            return response()->view('maintenance', compact('message'), 503);
        }

        return $next($request);
    }
}
