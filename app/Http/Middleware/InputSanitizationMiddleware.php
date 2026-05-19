<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InputSanitizationMiddleware
{
    protected array $except = [
        'password',
        'password_confirmation',
        'two_factor_secret',
        'two_factor_backup_codes',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('POST') || $request->isMethod('PUT') || $request->isMethod('PATCH')) {
            $request->merge($this->sanitizeInput($request->all()));
        }

        return $next($request);
    }

    protected function sanitizeInput(array $data): array
    {
        $sanitized = [];

        foreach ($data as $key => $value) {
            if (in_array($key, $this->except, true)) {
                $sanitized[$key] = $value;

                continue;
            }

            if (is_string($value)) {
                $cleaned = strip_tags($value);
                $cleaned = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $cleaned);
                $sanitized[$key] = trim($cleaned);
            } elseif (is_array($value)) {
                $sanitized[$key] = $this->sanitizeInput($value);
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }
}
