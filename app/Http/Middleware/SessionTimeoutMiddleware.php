<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeoutMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $timeout = $this->getTimeoutMinutes();
            $lastActivity = $request->session()->get('last_activity_at');

            if ($lastActivity) {
                $elapsed = now()->diffInMinutes($lastActivity);

                if ($elapsed >= $timeout) {
                    $user = Auth::user();
                    Auth::guard('web')->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    Log::warning('Session expired due to inactivity', [
                        'user_id' => $user?->id,
                        'email' => $user?->email,
                        'inactivity_minutes' => $elapsed,
                        'timeout' => $timeout,
                    ]);

                    return redirect()->route('login')
                        ->with('status', 'Your session has expired due to inactivity. Please log in again.');
                }

                if ($elapsed >= ($timeout - 5)) {
                    $request->session()->flash('session_expiring', true);
                    $request->session()->flash('session_expires_in', ($timeout - $elapsed) * 60);
                }
            }

            $request->session()->put('last_activity_at', now());
        }

        return $next($request);
    }

    private function getTimeoutMinutes(): int
    {
        try {
            $custom = app(Setting::class)
                ->where('key', 'session_lifetime')
                ->value('value');

            if ($custom) {
                return (int) $custom;
            }
        } catch (\Exception $e) {
            // Fall back to config default
        }

        return (int) config('session.lifetime', 120);
    }
}
