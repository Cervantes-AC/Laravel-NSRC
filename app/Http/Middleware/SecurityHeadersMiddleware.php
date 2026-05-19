<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    private function getSetting(string $key, string $default = '1'): string
    {
        try {
            return app(Setting::class)
                ->where('key', $key)
                ->value('value') ?? $default;
        } catch (\Exception $e) {
            return $default;
        }
    }

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $hstsEnabled = $this->getSetting('enable_hsts', '1') !== '0';
        $xFrameEnabled = $this->getSetting('enable_x_frame_options', '1') !== '0';
        $cspEnabled = $this->getSetting('enable_csp', '0') === '1';

        if ($hstsEnabled) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        if ($xFrameEnabled) {
            $response->headers->set('X-Frame-Options', 'DENY');
        }

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');
        $response->headers->set('Cross-Origin-Embedder-Policy', 'require-corp');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');

        if ($cspEnabled) {
            $response->headers->set('Content-Security-Policy', $this->buildCspPolicy());
        }

        return $response;
    }

    private function buildCspPolicy(): string
    {
        return implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net",
            "img-src 'self' data: blob:",
            "font-src 'self' https://fonts.gstatic.com",
            "connect-src 'self' https://*.firebaseio.com wss://*.firebaseio.com",
            "frame-src 'self'",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
        ]);
    }
}
