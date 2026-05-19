<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class XssProtectionMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (method_exists($response, 'header')) {
            $response->header('X-Content-Type-Options', 'nosniff');
            $response->header('X-XSS-Protection', '1; mode=block');
        }

        return $response;
    }

    public static function sanitize(mixed $value): mixed
    {
        if (is_string($value)) {
            $value = strip_tags($value);
            $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');

            return $value;
        }

        if (is_array($value)) {
            return array_map([self::class, 'sanitize'], $value);
        }

        return $value;
    }
}
