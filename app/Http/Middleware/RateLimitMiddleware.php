<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class RateLimitMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $maxRequests = config('app.rate_limit_max_requests', 100);
        $ip = $request->ip();
        $key = 'rate_limit:' . $ip;

        $attempts = (int) Cache::get($key, 0);

        if ($attempts >= $maxRequests) {
            return response()->json([
                'message' => 'Too many requests. Please try again later.',
            ], 429);
        }

        Cache::put($key, $attempts + 1, now()->addMinute());

        return $next($request);
    }
}
