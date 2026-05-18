<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || $user->status !== 'active') {
            abort(403, 'Unauthorized action.');
        }

        if (! in_array($user->role, $roles, true)) {
            if ($request->expectsJson()) {
                abort(403, 'Unauthorized action.');
            }

            return redirect()->route($user->role === 'admin' ? 'admin.dashboard' : 'member.dashboard');
        }

        return $next($request);
    }
}
