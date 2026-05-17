<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeoutMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return $next($request);
        }

        $timeoutMinutes = (int) config('attendance.session.timeout_minutes', 60);
        $lastActivity = $request->session()->get('last_activity_at');

        if ($lastActivity && now()->diffInMinutes($lastActivity) >= $timeoutMinutes) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('warning', 'Your session expired due to inactivity. Please sign in again.');
        }

        $request->session()->put('last_activity_at', now());

        return $next($request);
    }
}
