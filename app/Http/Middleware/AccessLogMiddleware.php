<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AccessLogMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($this->shouldLog($request, $response)) {
            $user = Auth::user();

            AuditLog::create([
                'user_id' => $user?->id,
                'full_name' => $user?->full_name ?? $user?->name ?? 'Guest',
                'type' => 'ACCESS',
                'action' => 'PAGE_VISIT',
                'details' => $request->method().' '.$request->path(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now(),
            ]);
        }

        return $response;
    }

    private function shouldLog(Request $request, Response $response): bool
    {
        return Auth::check()
            && $request->isMethod('GET')
            && $response->isSuccessful()
            && ! $request->expectsJson()
            && ! $request->is('api/*')
            && ! $request->is('up')
            && ! $request->is('two-factor-challenge');
    }
}
