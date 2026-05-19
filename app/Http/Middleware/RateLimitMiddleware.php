<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class RateLimitMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $maxRequests = $this->getMaxRequests();
        $decayMinutes = 1;
        $ip = $request->ip();
        $key = 'rate_limit:'.$ip;

        $attempts = (int) Cache::get($key, 0);

        $response = $next($request);

        $response->headers->set('X-RateLimit-Limit', $maxRequests);
        $response->headers->set('X-RateLimit-Remaining', max(0, $maxRequests - $attempts - 1));
        $response->headers->set('Retry-After', $decayMinutes * 60);

        if ($attempts >= $maxRequests) {
            Cache::put($key, $attempts + 1, now()->addMinutes($decayMinutes));

            return response()->json([
                'message' => 'Too many requests. Please try again later.',
                'retry_after_seconds' => $decayMinutes * 60,
            ], 429, [
                'X-RateLimit-Limit' => $maxRequests,
                'X-RateLimit-Remaining' => 0,
                'Retry-After' => $decayMinutes * 60,
            ]);
        }

        Cache::put($key, $attempts + 1, now()->addMinutes($decayMinutes));

        return $response;
    }

    private function getMaxRequests(): int
    {
        try {
            $custom = app(Setting::class)
                ->where('key', 'rate_limit_max_attempts')
                ->value('value');

            if ($custom) {
                return (int) $custom;
            }
        } catch (\Exception $e) {
            // Fall back to config default
        }

        return (int) config('app.rate_limit_max_requests', 100);
    }
}
