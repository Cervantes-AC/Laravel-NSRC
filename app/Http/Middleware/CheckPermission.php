<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401);
        }

        $parts = explode('.', $permission, 2);
        $module = $parts[0];
        $action = $parts[1] ?? 'view';

        $permissions = config("permissions.{$user->role}", []);

        if (! isset($permissions[$module]) || ! in_array($action, $permissions[$module], true)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden: insufficient permissions.'], 403);
            }

            abort(403, 'You do not have permission to perform this action.');
        }

        return $next($request);
    }
}
